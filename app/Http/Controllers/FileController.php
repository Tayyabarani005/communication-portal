<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\DmConversation;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            $attachable = \App\Models\Message::findOrFail($request->message_id ?? 0);
        } else {
            $conversation = DmConversation::findOrFail($request->attachable_id);
            $this->authorize('view', $conversation);
            $attachable = \App\Models\DirectMessage::findOrFail($request->message_id ?? 0);
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

    public function download(File $file): Response
    {
        $attachable = $file->attachable;

        // Authorize based on parent model type
        if ($attachable instanceof \App\Models\Message) {
            $this->authorize('view', $attachable->channel);
        } elseif ($attachable instanceof \App\Models\DirectMessage) {
            $this->authorize('view', $attachable->conversation);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $file->file_name
        );
    }
}
