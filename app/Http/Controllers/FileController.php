<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\DmConversation;
use App\Models\DirectMessage;
use App\Models\DmParticipant;
use App\Models\File;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'text/plain',
        'text/csv',
    ];

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file'           => ['required', 'file', 'max:10240', 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'attachable_type' => ['required', 'string', 'in:channel,dm'],
            'attachable_id'   => ['required', 'integer'],
        ]);

        if ($request->attachable_type === 'channel') {
            $channel = Channel::findOrFail($request->attachable_id);
            $this->authorize('sendMessage', $channel);
            $attachable = Message::findOrFail($request->message_id ?? 0);
        } else {
            $conversation = DmConversation::findOrFail($request->attachable_id);
            $this->authorizeDirectMessageConversation($conversation);
            $attachable = DirectMessage::findOrFail($request->message_id ?? 0);
        }

        $file = $request->file('file');
        $uuid = Str::uuid();
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs(
            "attachments/{$attachable->getKey()}",
            "{$uuid}-{$originalName}",
            'public'
        );

        File::create([
            'attachable_id'   => $attachable->getKey(),
            'attachable_type' => get_class($attachable),
            'file_name'       => $originalName,
            'file_path'       => $path,
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
        ]);

        return back()->with('success', 'File uploaded.');
    }

    public function download(File $file): \Symfony\Component\HttpFoundation\Response
    {
        $attachable = $file->attachable;

        // Authorize based on parent model type
        if ($attachable instanceof Message) {
            $this->authorize('view', $attachable->channel);
        } elseif ($attachable instanceof DirectMessage) {
            $this->authorizeDirectMessageConversation($attachable->conversation);
        } else {
            abort(404, 'File not found.');
        }

        $storedFile = $this->resolveStoredFile($file->file_path);

        if ($storedFile === null) {
            abort(404, 'File not found.');
        }

        [$diskName, $path] = $storedFile;
        $disk = Storage::disk($diskName);

        return response()->streamDownload(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);

            if ($stream === false) {
                return;
            }

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $file->file_name, array_filter([
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Content-Length' => $file->file_size ? (string) $file->file_size : null,
        ]));
    }

    private function authorizeDirectMessageConversation(DmConversation $conversation): void
    {
        $isParticipant = DmParticipant::where('conversation_id', $conversation->conversation_id)
            ->where('user_id', auth()->user()->user_id)
            ->exists();

        abort_unless($isParticipant, 403);
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function resolveStoredFile(?string $filePath): ?array
    {
        if (!$filePath) {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', ltrim($filePath, '/'));
        $candidates = [
            ['public', $normalizedPath],
        ];

        $urlPath = parse_url($filePath, PHP_URL_PATH);
        if (is_string($urlPath)) {
            $normalizedUrlPath = ltrim(str_replace('\\', '/', $urlPath), '/');
            $candidates[] = ['public', $normalizedUrlPath];

            if (Str::contains($normalizedUrlPath, 'storage/')) {
                $candidates[] = ['public', Str::after($normalizedUrlPath, 'storage/')];
            }
        }

        if (Str::startsWith($normalizedPath, 'storage/')) {
            $candidates[] = ['public', Str::after($normalizedPath, 'storage/')];
        }

        if (Str::startsWith($normalizedPath, 'public/')) {
            $candidates[] = ['public', Str::after($normalizedPath, 'public/')];
        }

        $candidates[] = ['local', $normalizedPath];

        foreach ($candidates as [$diskName, $path]) {
            if ($path !== '' && Storage::disk($diskName)->exists($path)) {
                return [$diskName, $path];
            }
        }

        return null;
    }
}
