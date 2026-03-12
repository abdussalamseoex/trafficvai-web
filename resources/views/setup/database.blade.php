<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration | TrafficVai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0F1117] text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">TrafficVai</h1>
            <p class="text-gray-400 mt-2">Database Setup Wizard</p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">
            <form id="setupForm" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Database Host</label>
                    <input type="text" name="db_host" value="127.0.0.1" required
                        class="w-full bg-black border border-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Database Name</label>
                    <input type="text" name="db_name" placeholder="e.g. trafficvai_db" required
                        class="w-full bg-black border border-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Database Username</label>
                    <input type="text" name="db_user" placeholder="root" required
                        class="w-full bg-black border border-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Database Password</label>
                    <input type="password" name="db_pass" placeholder="••••••••"
                        class="w-full bg-black border border-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>

                <div id="statusMessage" class="hidden text-sm p-4 rounded-xl border"></div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-900/20 transform transition active:scale-[0.98]">
                    Connect & Setup
                </button>
            </form>
        </div>

        <div class="mt-6 text-center text-gray-500 text-xs">
            &copy; {{ date('Y') }} TrafficVai SEO Platform. Secure Installation.
        </div>
    </div>

    <script>
        document.getElementById('setupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const status = document.getElementById('statusMessage');
            const formData = new FormData(e.target);

            btn.disabled = true;
            btn.innerText = 'Connecting...';
            status.classList.add('hidden');

            try {
                // Step 1: Save .env
                const response = await fetch('/setup', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });

                const data = await response.json();
                if (!data.success) throw new Error(data.message);

                status.className = 'text-blue-400 bg-blue-400/10 border-blue-400/20 text-sm p-4 rounded-xl block';
                status.innerText = 'Environment saved! Finalizing database tables...';
                
                // Step 2: Run Migrations (Fresh Request)
                const migrateResp = await fetch('/setup/migrate', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                const migrateData = await migrateResp.json();
                if (!migrateData.success) throw new Error(migrateData.message);

                status.className = 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20 text-sm p-4 rounded-xl block';
                status.innerText = 'Success! System is ready. Redirecting...';
                setTimeout(() => window.location.href = '/login', 2000);

            } catch (err) {
                status.className = 'text-rose-400 bg-rose-400/10 border-rose-400/20 text-sm p-4 rounded-xl block';
                status.innerText = err.message;
                btn.disabled = false;
                btn.innerText = 'Connect & Setup';
            }
        });
    </script>
</body>
</html>
