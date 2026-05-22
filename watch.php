<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting | Christ Embassy Barking</title>
    <meta http-equiv="refresh" content="5;url=stream-register.html">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: var(--light-gray);">
    <main style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div style="max-width: 36rem; text-align: center; background: white; padding: 2.5rem; border-radius: 1.5rem;" class="sleek-shadow">
            <p style="letter-spacing: 0.18em; text-transform: uppercase; font-size: 0.78rem; font-weight: 700; color: var(--accent-orange); margin-bottom: 1rem;">
                Watch Service
            </p>
            <h1 style="font-size: 2rem; line-height: 1.1; color: var(--primary-blue); margin-bottom: 1rem;">
                Redirecting you now
            </h1>
            <p style="color: #5f6f82; margin-bottom: 1.5rem;">
                We are checking whether you are a returning viewer so we can send you to the right page.
            </p>
            <p style="color: #5f6f82;">
                If nothing happens, <a href="stream-register.html" style="color: var(--accent-orange); font-weight: 600;">continue to registration</a>.
            </p>
        </div>
    </main>

    <script>
        (function () {
            const hasViewerCookie = document.cookie
                .split(';')
                .map(part => part.trim())
                .some(part => part.startsWith('viewer_id='));

            window.location.replace(hasViewerCookie ? 'returning-viewer.html' : 'stream-register.html');
        })();
    </script>
</body>
</html>
