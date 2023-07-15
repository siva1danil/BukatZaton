window.addEventListener('load', () => {
    // Prepare variables/functions
    const UI = {
        formToggle: document.getElementById('form-toggle'),
        formContainer: document.getElementById('form-container'),
        formSuccess: document.getElementById('form-success'),
        form: document.getElementById('form'),
        formComment: document.getElementById('form-comment'),
        fieldUsername: document.getElementById('form').username,
        fieldEmail: document.getElementById('form').email,
        fieldPhone: document.getElementById('form').phone,
        fieldReview: document.getElementById('form').review,
        publishButton: document.getElementById('form').button
    };
    const Verifications = {
        nonEmpty: string => !/^(\s+)?$/.test(string),
        email: string => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(string),
        phone: string => string.replace(/\D/g, '').length == 11
    };

    UI.formToggle.addEventListener('click', event => {
        event.preventDefault();
        UI.formContainer.style.display = UI.formContainer.style.display == 'none' ? 'block' : 'none';
    });
    UI.form.addEventListener('submit', async event => {
        event.preventDefault();
        UI.publishButton.disabled = true;

        let data = {
            username: UI.fieldUsername.value,
            email: UI.fieldEmail.value,
            phone: UI.fieldPhone.value,
            rating: parseInt(UI.form.rating.value),
            review: UI.fieldReview.value
        };

        UI.fieldUsername.style.borderColor = null;
        UI.fieldEmail.style.borderColor = null;
        UI.fieldPhone.style.borderColor = null;
        UI.fieldReview.style.borderColor = null;

        let valid = true;
        if(!Verifications.nonEmpty(data.username) || data.username.length > 64) {
            UI.fieldUsername.style.borderColor = 'red';
            valid = false;
        }
        if((!Verifications.email(data.email) && Verifications.nonEmpty(data.email)) || data.email.length > 64) {
            UI.fieldEmail.style.borderColor = 'red';
            valid = false;
        }
        if(!Verifications.phone(data.phone) && Verifications.nonEmpty(data.phone)) {
            UI.fieldPhone.style.borderColor = 'red';
            valid = false;
        }
        if(!Verifications.nonEmpty(data.review)) {
            UI.fieldReview.style.borderColor = 'red';
            valid = false;
        }

        if(valid) {
            try {
                let res = await fetch('/api/add_review.php', {
                    method: 'POST',
                    body: JSON.stringify(data)
                }).then(res => res.json());
                if(res.error) UI.formComment.innerText = res.error;
                else {
                    UI.formSuccess.style.display = null;
                    UI.form.style.display = 'none';
                }
            } catch(error) {
                console.log(error);
                UI.formComment.innerText = 'Не удалось отправить отзыв!';
            }
        } else {
            UI.formComment.innerText = 'Выделенные поля заполнены неверно';
        }
        UI.publishButton.disabled = false;
    });
});