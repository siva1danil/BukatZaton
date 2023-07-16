window.addEventListener('load', () => {
    if(document.getElementById("login")) {
        const UI = {
            fieldUsername: document.getElementById('field-login'),
            fieldPassword: document.getElementById('field-password'),
            button: document.getElementById('button-login'),
            status: document.getElementById('status')
        };
        console.log(UI);

        UI.button.addEventListener('click', async () => {
            UI.button.disabled = true;

            let data = {
                username: UI.fieldUsername.value,
                password: UI.fieldPassword.value
            };

            try {
                let res = await fetch('/api/login.php', {
                    method: 'POST',
                    body: JSON.stringify(data)
                }).then(res => res.json());
                if(res.error) UI.status.innerText = res.error;
                else location.reload();
            } catch(error) {
                console.log(error);
                UI.status.innerText = 'Не удалось войти!';
            }

            UI.button.disabled = false;
        });
    }
});