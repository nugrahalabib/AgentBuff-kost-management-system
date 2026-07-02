<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-6">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.664 1.732-2.992l-6.928-12A2 2 0 0015 2.992 2 2 0 0012 2a2 2 0 00-1.732 1.008l-6.928 12C2.608 19.336 3.57 21 5.11 21z"/>
                    </svg>
                </div>
                
                <h1 class="text-6xl font-bold text-gray-900 mb-2">403</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>
                <p class="text-gray-600 mb-8">
                    {{ $exception->getMessage() ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
                </p>
                
                <div class="space-y-3">
                    <a href="{{ url()->previous() }}" class="inline-block w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Kembali ke Halaman Sebelumnya
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-block w-full px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
