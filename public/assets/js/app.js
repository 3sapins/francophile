/**
 * FRANCOPHILE.CH - JavaScript principal
 */

const Francophile = {
    config: { apiUrl: '/api' },

    $(selector) { return document.querySelector(selector); },
    $$(selector) { return document.querySelectorAll(selector); },

    async post(endpoint, data) {
        const response = await fetch(this.config.apiUrl + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return response.json();
    },

    notify(message, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        const container = this.$('.main-content') || document.body;
        container.insertBefore(alert, container.firstChild);
        setTimeout(() => alert.remove(), 4000);
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Menu mobile
document.addEventListener('DOMContentLoaded', () => {
    const toggle = Francophile.$('.mobile-menu-toggle');
    const nav = Francophile.$('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => nav.classList.toggle('open'));
    }
});

// Tabs login
document.addEventListener('DOMContentLoaded', () => {
    Francophile.$$('.login-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            Francophile.$$('.login-tab').forEach(t => t.classList.remove('active'));
            Francophile.$$('.login-form').forEach(f => f.classList.remove('active'));
            tab.classList.add('active');
            const form = Francophile.$(`#form-${tab.dataset.tab}`);
            if (form) form.classList.add('active');
        });
    });
});

// Gestionnaire d'exercices
const ExerciceManager = {
    sessionId: null,
    exercices: [],
    currentIndex: 0,
    score: 0,
    questionStartTime: null,

    init(exercices, sessionId) {
        this.exercices = exercices;
        this.sessionId = sessionId;
        this.currentIndex = 0;
        this.score = 0;
        this.afficherExercice();
        this.updateProgress();
    },

    afficherExercice() {
        const ex = this.exercices[this.currentIndex];
        if (!ex) return;
        this.questionStartTime = Date.now();

        const container = Francophile.$('.exercice-card');
        if (!container) return;

        let question = ex.question.replace('___', '<span class="blank"></span>');
        let inputHtml = '';

        if (ex.options?.length) {
            inputHtml = '<div class="exercice-options">' + 
                ex.options.map(o => `<button class="exercice-option" data-value="${Francophile.escapeHtml(o)}">${Francophile.escapeHtml(o)}</button>`).join('') + 
                '</div>';
        } else {
            inputHtml = '<div class="exercice-input"><input type="text" class="form-input" id="reponse-input" placeholder="Ta réponse..." autofocus></div>';
        }

        container.innerHTML = `
            <div class="exercice-question">${question}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            ${inputHtml}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>
        `;

        this.attachEvents();
    },

    attachEvents() {
        Francophile.$$('.exercice-option').forEach(opt => {
            opt.addEventListener('click', () => {
                Francophile.$$('.exercice-option').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });

        Francophile.$('#btn-valider')?.addEventListener('click', () => this.validerReponse());
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());

        const input = Francophile.$('#reponse-input');
        if (input) {
            input.focus();
            input.addEventListener('keypress', e => { if (e.key === 'Enter') this.validerReponse(); });
        }
    },

    async validerReponse() {
        const ex = this.exercices[this.currentIndex];
        const selected = Francophile.$('.exercice-option.selected');
        const input = Francophile.$('#reponse-input');
        const reponse = selected?.dataset.value || input?.value.trim() || '';

        if (!reponse) return Francophile.notify('Entre une réponse', 'warning');

        const correct = reponse.toLowerCase() === ex.reponse_correcte.toLowerCase();
        if (correct) this.score++;

        this.afficherFeedback(correct, ex.reponse_correcte, ex.explication);

        try {
            await Francophile.post('/save_reponse.php', {
                session_id: this.sessionId,
                exercice_type: ex.type,
                exercice_id: ex.id || 0,
                question: ex.question,
                reponse_attendue: ex.reponse_correcte,
                reponse_donnee: reponse,
                correct,
                temps_reponse: Math.round((Date.now() - this.questionStartTime) / 1000)
            });
        } catch (e) { console.error(e); }

        Francophile.$('#btn-valider').classList.add('hidden');
        Francophile.$('#btn-suivant').classList.remove('hidden');
        Francophile.$$('.exercice-option').forEach(o => o.style.pointerEvents = 'none');
        if (input) input.disabled = true;
    },

    afficherFeedback(correct, reponseCorrecte, explication) {
        const fb = Francophile.$('.exercice-feedback');
        fb.classList.remove('hidden', 'correct', 'incorrect');
        fb.classList.add(correct ? 'correct' : 'incorrect');

        fb.innerHTML = `
            <div class="feedback-icon">${correct ? '✓' : '✗'}</div>
            <div>${correct ? 'Bravo !' : 'Dommage...'}</div>
            ${!correct ? `<div>Réponse : <strong>${Francophile.escapeHtml(reponseCorrecte)}</strong></div>` : ''}
            ${explication ? `<div class="text-small text-muted mt-sm">${Francophile.escapeHtml(explication)}</div>` : ''}
        `;

        Francophile.$$('.exercice-option').forEach(o => {
            if (o.dataset.value.toLowerCase() === reponseCorrecte.toLowerCase()) o.classList.add('correct');
            else if (o.classList.contains('selected')) o.classList.add('incorrect');
        });
    },

    suivant() {
        this.currentIndex++;
        if (this.currentIndex >= this.exercices.length) this.terminer();
        else { this.afficherExercice(); this.updateProgress(); }
    },

    updateProgress() {
        const counter = Francophile.$('.exercice-counter');
        if (counter) counter.textContent = `${this.currentIndex + 1} / ${this.exercices.length}`;
        const bar = Francophile.$('.progress-bar');
        if (bar) bar.style.width = `${(this.currentIndex / this.exercices.length) * 100}%`;
    },

    async terminer() {
        try {
            const result = await Francophile.post('/terminer_session.php', { session_id: this.sessionId });
            this.afficherResultats(result);
        } catch (e) {
            console.error(e);
            Francophile.notify('Erreur sauvegarde', 'error');
        }
    },

    afficherResultats(result) {
        const container = Francophile.$('.exercice-container');
        const taux = result.taux_reussite || Math.round((this.score / this.exercices.length) * 100);
        const pts = result.points_nets || 0;

        container.innerHTML = `
            <div class="card">
                <div class="card-body text-center">
                    <h2>Session terminée !</h2>
                    <div class="stat-value" style="font-size:3rem;color:${taux >= 70 ? 'var(--success)' : taux >= 50 ? 'var(--warning)' : 'var(--error)'}">${taux}%</div>
                    <p><strong>${this.score}</strong> / ${this.exercices.length} bonnes réponses</p>
                    <p style="font-size:1.25rem;color:${pts >= 0 ? 'var(--success)' : 'var(--error)'}">${pts >= 0 ? '+' : ''}${pts} points</p>
                    ${result.progression?.level_up ? '<p class="alert alert-success">🎉 Niveau supérieur atteint !</p>' : ''}
                    <div class="mt-lg">
                        <a href="/eleve/exercices.php" class="btn btn-outline">Autres exercices</a>
                        <a href="/eleve/dashboard.php" class="btn btn-primary">Tableau de bord</a>
                    </div>
                </div>
            </div>
        `;
    }
};

// Gestionnaire classes (enseignant)
const ClasseManager = {
    copyCode(code) {
        navigator.clipboard.writeText(code).then(() => Francophile.notify('Code copié !', 'success'));
    }
};

window.Francophile = Francophile;
window.ExerciceManager = ExerciceManager;
window.ClasseManager = ClasseManager;
