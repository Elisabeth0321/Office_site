import { CAPTCHA_SITE_KEY } from './config.js';

class RegistrationForm {
    constructor(formId, errorContainerId) {
        this.form = document.getElementById(formId);
        this.errorBox = document.getElementById(errorContainerId);
        this.init();
    }

    init() {
        this.renderRecaptcha();
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    renderRecaptcha() {
        const interval = setInterval(() => {
            if (window.grecaptcha) {
                grecaptcha.render(document.querySelector('.g-recaptcha'), {
                    sitekey: CAPTCHA_SITE_KEY
                });
                clearInterval(interval);
            }
        }, 200);
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.errorBox.textContent = '';

        const captchaToken = grecaptcha.getResponse();
        if (!captchaToken) {
            this.showError('Пожалуйста, подтвердите капчу.');
            return;
        }

        const firstname = this.getValue('firstname');
        const lastname = this.getValue('lastname');
        const email = this.getValue('email');
        const password = this.getValue('password');

        if (!firstname || !email || !password) {
            this.showError('Все поля обязательны для заполнения.');
            return;
        }

        const payload = {
            firstname,
            lastname,
            email,
            password,
            captcha: captchaToken };

        try {
            const response = await fetch('/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                this.showError(data.message || 'Произошла ошибка.');
            } else {
                alert('Письмо с подтверждением отправлено. Проверьте почту.');
                window.location.href = '/office-manager';
            }
        } catch {
            this.showError('Ошибка соединения с сервером.');
        }
    }

    getValue(id) {
        return document.getElementById(id)?.value.trim() || '';
    }

    showError(message) {
        this.errorBox.textContent = message;
        grecaptcha.reset();
    }
}

export default RegistrationForm;
