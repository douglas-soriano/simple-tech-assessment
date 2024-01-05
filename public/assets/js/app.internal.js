// API :: Autenticação
axios.defaults.headers.common = {
    'X-CSRF-TOKEN': API.csrfToken,
    'X-Requested-With': 'XMLHttpRequest',
    'Authorization': 'Bearer ' + API.apiToken,
};

