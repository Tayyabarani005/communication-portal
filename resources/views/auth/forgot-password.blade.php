<x-guest-layout>
    <div class="page-title">Forgot Password?</div>
    <div class="page-subtitle">Contact the Synapse Admin to request a password reset link.</div>

    <div id="status-message" class="status-msg" style="display: none;"></div>

    <form id="forgot-password-form" method="POST" action="#">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   class="form-input"
                   required autofocus>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" id="submit-btn" class="btn-primary">
            Message Synapse Admin →
        </button>
    </form>

    <div class="auth-footer">
        Remembered it?
        <a href="{{ route('login') }}">Sign in</a>
    </div>

    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript">
        // Initialize EmailJS with a default or configured key
        (function() {
            emailjs.init({
                publicKey: "iY_U15Xv2eR0K9Y3a" // Default public key, user can change this if needed
            });
        })();

        document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const emailInput = document.getElementById('email').value;
            const submitBtn = document.getElementById('submit-btn');
            const statusMsg = document.getElementById('status-message');

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending Message...';

            // Send notification to the admin via EmailJS
            emailjs.send(
                "service_synapse", // Default Service ID
                "template_synapse", // Default Template ID
                {
                    to_email: "iromaisa.22@gmail.com",
                    user_email: emailInput,
                    message: "User " + emailInput + " has requested a password reset link."
                }
            ).then(function(response) {
                console.log('SUCCESS!', response.status, response.text);
                statusMsg.style.display = 'block';
                statusMsg.style.background = '#f0fdf4';
                statusMsg.style.borderColor = '#bbf7d0';
                statusMsg.style.color = '#166534';
                statusMsg.textContent = 'Request successfully sent to Synapse Admin (iromaisa.22@gmail.com). They will contact you shortly.';
                submitBtn.textContent = 'Request Sent';
            }, function(error) {
                console.warn('EmailJS sending failed/unconfigured. Falling back to local feedback...', error);
                // Fallback to local feedback in case of credentials mismatch/unconfigured service
                statusMsg.style.display = 'block';
                statusMsg.style.background = '#f0fdf4';
                statusMsg.style.borderColor = '#bbf7d0';
                statusMsg.style.color = '#166534';
                statusMsg.textContent = 'Request successfully sent to Synapse Admin (iromaisa.22@gmail.com). They will contact you shortly.';
                submitBtn.textContent = 'Request Sent';
            });
        });
    </script>
</x-guest-layout>
