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
            if(UI.button.disabled) return;
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
    } else {
        const UI = {
            fatal: document.getElementById('fatal'),
            reviewTemplate: document.getElementById('template-review'),
            logout: document.getElementById('button-logout'),
            tabHeaderPublic: document.getElementById('tabs-header-public'),
            tabHeaderPrivate: document.getElementById('tabs-header-private'),
            tabContentPublic: document.getElementById('tabs-content-public'),
            tabContentPrivate: document.getElementById('tabs-content-private'),
            countPublic: document.getElementById('reviews-public-count'),
            countPrivate: document.getElementById('reviews-private-count'),
            reviewsPublic: document.getElementById('reviews-public'),
            reviewsPrivate: document.getElementById('reviews-private'),
            editBackground: document.getElementById('edit-background'),
            editForm: document.getElementById('edit-form'),
            editCancel: document.getElementById('edit-cancel'),
            editSave: document.getElementById('edit-save')
        };
        const Actions = {
            prettyPrintPhone: phone => `+${~~(phone / 10000000000)} ${~~(phone % 10000000000 / 10000000)} ${~~(phone % 10000000 / 10000)} ${~~(phone % 10000)}`,
            setReviews: reviews => {
                UI.reviewsPublic.innerText = '';
                UI.reviewsPrivate.innerText = '';

                for(let review of reviews) {
                    let element = UI.reviewTemplate.content.children[0].cloneNode(true);

                    for(let i = review.rating; i < 5; i++)
                        element.getElementsByClassName('review-stars')[0].children[i].style.color = 'transparent';
                    element.getElementsByClassName('review-name')[0].innerText = review.username;
                    if(review.phone != 0)
                        element.getElementsByClassName('review-phone')[0].innerText = Actions.prettyPrintPhone(review.phone);
                    if(review.email != "")
                        element.getElementsByClassName('review-email')[0].innerText = review.email;
                    element.getElementsByClassName('review-text')[0].innerText = review.review;

                    element.getElementsByClassName('review-change-publicity')[0].addEventListener('click', async event => {
                        if(event.target.disabled) return;
                        event.target.disabled = true;
                        element.style.opacity = 0.5;

                        await API.setPublicity(review.id, !review.public)
                            .then(() => Actions.reload())
                            .catch(error => alert(error.message));

                        event.target.disabled = false;
                        element.style.opacity = 1.0;
                    });
                    element.getElementsByClassName('review-edit')[0].addEventListener('click', async event => {
                        if(event.target.disabled) return;
                        event.target.disabled = true;
                        element.style.opacity = 0.5;

                        while(true) {
                            newReview = await Actions.edit(review);
                            if(newReview == null) break;
                            else review = newReview;

                            try {
                                await API.editReview(review).then(() => Actions.reload());
                                break;
                            } catch(error) {
                                alert(error.message);
                            };
                        }

                        event.target.disabled = false;
                        element.style.opacity = 1.0;
                    });
                    element.getElementsByClassName('review-delete')[0].addEventListener('click', async event => {
                        if(event.target.disabled) return;
                        event.target.disabled = true;
                        element.style.opacity = 0.5;

                        await API.deleteReview(review.id)
                            .then(() => Actions.reload())
                            .catch(error => alert(error.message));

                        event.target.disabled = false;
                        element.style.opacity = 1.0;
                    });

                    if(review.public)
                        UI.reviewsPublic.appendChild(element);
                    else
                        UI.reviewsPrivate.appendChild(element);
                }

                UI.countPublic.innerText = reviews.filter(item => item.public).length;
                UI.countPrivate.innerText = reviews.filter(item => !item.public).length;
            },
            reload: async () => {
                await API.getReviews().then(data => Actions.setReviews(data));
            },
            edit: async review => new Promise((resolve, reject) => {
                UI.editBackground.style.display = 'flex';
                const cancel = UI.editCancel.cloneNode(true);
                UI.editCancel.replaceWith(cancel);
                UI.editCancel = cancel;
                const save = UI.editSave.cloneNode(true);
                UI.editSave.replaceWith(save);
                UI.editSave = save;
                UI.editForm.username.value = review.username;
                UI.editForm.phone.value = review.phone == 0 ? '' : review.phone;
                UI.editForm.email.value = review.email;
                UI.editForm.rating.value = review.rating;
                UI.editForm.review.value = review.review;
                UI.editCancel.addEventListener('click', () => {
                    UI.editBackground.style.display = 'none';
                    resolve(null);
                });
                UI.editSave.addEventListener('click', () => {
                    UI.editBackground.style.display = 'none';
                    resolve({ id: review.id, name: review.public, date: review.date,
                        username: UI.editForm.username.value, email: UI.editForm.email.value,
                        phone: UI.editForm.phone.value, rating: UI.editForm.rating.value,
                        review: UI.editForm.review.value });
                });
            }),
            setTab(headers, contents, header, content) {
                for(let header of headers)
                    header.classList.remove('tabs-header-entry-active');
                for(let content of contents)
                    content.classList.remove('tab-active');
                header.classList.add('tabs-header-entry-active');
                content.classList.add('tab-active');
            }
        };
        const API = {
            logout: async () => {
                let data = await fetch('/api/logout.php')
                    .then(res => res.json());
                if(data.error) throw new Error(data.error);
            },
            getReviews: async () => {
                let data = [];
                for(let i = 1; true; i++) {
                    let page = await fetch(`/api/get_reviews.php?page=${i}`)
                        .then(res => res.json());
                    if(page.error) throw new Error(page.error);
                    else if(page.data.length != 0) data.push(...page.data);
                    else break;
                }
                return data;
            },
            deleteReview: async id => {
                let data = await fetch('/api/delete_review.php', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                }).then(res => res.json());
                if(data.error) throw new Error(data.error);
            },
            setPublicity: async (id, public) => {
                let data = await fetch('/api/set_publicity.php', {
                    method: 'POST',
                    body: JSON.stringify({ id, public })
                }).then(res => res.json());
                if(data.error) throw new Error(data.error);
            },
            editReview: async review => {
                let data = await fetch('/api/edit_review.php', {
                    method: 'POST',
                    body: JSON.stringify(review)
                }).then(res => res.json());
                if(data.error) throw new Error(data.error);
            }
        };

        UI.logout.addEventListener('click', async () => {
            if(UI.logout.disabled) return;
            UI.logout.disabled = true;

            await API.logout()
                .then(() => location.reload())
                .catch(error => alert(error.message));
            
            UI.logout.disabled = false;
        });
        UI.tabHeaderPublic.addEventListener('click', event => {
            event.preventDefault();
            Actions.setTab([ UI.tabHeaderPublic, UI.tabHeaderPrivate ], [ UI.tabContentPublic, UI.tabContentPrivate ], UI.tabHeaderPublic, UI.tabContentPublic);
        });
        UI.tabHeaderPrivate.addEventListener('click', event => {
            event.preventDefault();
            Actions.setTab([ UI.tabHeaderPublic, UI.tabHeaderPrivate ], [ UI.tabContentPublic, UI.tabContentPrivate ], UI.tabHeaderPrivate, UI.tabContentPrivate);
        });

        Actions.reload()
            .then(() => {
                Actions.setTab([ UI.tabHeaderPublic, UI.tabHeaderPrivate ], [ UI.tabContentPublic, UI.tabContentPrivate ], UI.tabHeaderPublic, UI.tabContentPublic);
                fatal.innerText = '';
            }).catch(error => fatal.innerText = error.message);
    }
});