import './bootstrap';

async function checkAuth() {
    try {
        const response = await fetch('/api/user', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok && response.status === 401) {
            window.location.href = '/login-page';
        }
    } catch {
        window.location.href = '/login-page';
    }
}

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        checkAuth();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const btnFetch = document.getElementById('btn-fetch-api');
    const apiOutput = document.getElementById('api-output');

    if (btnFetch && apiOutput) {
        btnFetch.addEventListener('click', async () => {
            apiOutput.textContent = 'Memuat data dari API...';
            try {
                const response = await fetch('/api/user', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const json = await response.json();
                apiOutput.textContent = JSON.stringify(json, null, 4);
                apiOutput.classList.remove('text-rose-400');
                apiOutput.classList.add('text-emerald-400');
            } catch (error) {
                apiOutput.textContent = `Error mengambil data: ${error.message}`;
                apiOutput.classList.remove('text-emerald-400');
                apiOutput.classList.add('text-rose-400');
            }
        });
    }
});

