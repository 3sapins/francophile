/**
 * FRANCOPHILE.CH - JavaScript principal v9
 * Moteur d'exercices enrichi : 6 nouveaux formats + gamification
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
        alert.className = `alert alert-${type} notification-toast`;
        alert.textContent = message;
        document.body.appendChild(alert);
        requestAnimationFrame(() => alert.classList.add('show'));
        setTimeout(() => { alert.classList.remove('show'); setTimeout(() => alert.remove(), 300); }, 3000);
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
    if (toggle && nav) toggle.addEventListener('click', () => nav.classList.toggle('open'));
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

// ============================================
// EXERCISE ENGINE v9
// ============================================
const ExerciceManager = {
    sessionId: null,
    exercices: [],
    currentIndex: 0,
    score: 0,
    streak: 0,
    bestStreak: 0,
    questionStartTime: null,
    timerInterval: null,
    timerEnabled: false,
    timerLimit: 30,

    init(exercices, sessionId, options = {}) {
        this.exercices = exercices;
        this.sessionId = sessionId;
        this.currentIndex = 0;
        this.score = 0;
        this.streak = 0;
        this.bestStreak = 0;
        this.timerEnabled = options.timer || false;
        this.timerLimit = options.timerLimit || 30;
        this.afficherExercice();
        this.updateProgress();
    },

    // ---- MAIN ROUTER ----
    afficherExercice() {
        const ex = this.exercices[this.currentIndex];
        if (!ex) return;
        this.questionStartTime = Date.now();
        this.stopTimer();

        const container = Francophile.$('.exercice-card');
        if (!container) return;

        const format = ex.format || 'qcm';
        switch (format) {
            case 'qcm':           this.renderQCM(container, ex); break;
            case 'input':         this.renderInput(container, ex); break;
            case 'drag_order':    this.renderDragOrder(container, ex); break;
            case 'drag_category': this.renderDragCategory(container, ex); break;
            case 'vrai_faux':     this.renderVraiFaux(container, ex); break;
            case 'transformation':this.renderTransformation(container, ex); break;
            case 'intrus':        this.renderIntrus(container, ex); break;
            case 'multi_trous':   this.renderMultiTrous(container, ex); break;
            default:              this.renderQCM(container, ex); break;
        }

        if (this.timerEnabled) this.startTimer();
    },

    // ---- FORMAT: QCM ----
    renderQCM(container, ex) {
        let question = ex.question.replace('___', '<span class="blank"></span>');
        container.innerHTML = `
            <div class="exercice-type-badge">QCM</div>
            ${this.streakHtml()}
            <div class="exercice-question">${question}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            <div class="exercice-options">
                ${(ex.options||[]).map(o => `<button class="exercice-option" data-value="${Francophile.escapeHtml(o)}">${Francophile.escapeHtml(o)}</button>`).join('')}
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        Francophile.$$('.exercice-option').forEach(opt => {
            opt.addEventListener('click', () => {
                Francophile.$$('.exercice-option').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            const sel = Francophile.$('.exercice-option.selected');
            if (!sel) return Francophile.notify('Choisis une réponse', 'warning');
            this.submitAnswer(sel.dataset.value);
        });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: INPUT ----
    renderInput(container, ex) {
        let question = ex.question.replace('___', '<span class="blank"></span>');
        container.innerHTML = `
            <div class="exercice-type-badge">Écriture</div>
            ${this.streakHtml()}
            <div class="exercice-question">${question}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            <div class="exercice-input">
                <input type="text" class="form-input" id="reponse-input" placeholder="Ta réponse..." autocomplete="off" autocapitalize="off" spellcheck="false" autofocus>
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        const inp = Francophile.$('#reponse-input');
        inp?.focus();
        inp?.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); this.submitInputAnswer(); } });
        Francophile.$('#btn-valider')?.addEventListener('click', () => this.submitInputAnswer());
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    submitInputAnswer() {
        const inp = Francophile.$('#reponse-input');
        const r = inp?.value.trim() || '';
        if (!r) return Francophile.notify('Entre une réponse', 'warning');
        inp.disabled = true;
        this.submitAnswer(r);
    },

    // ---- FORMAT: DRAG ORDER (remettre en ordre) ----
    renderDragOrder(container, ex) {
        const frags = [...ex.fragments];
        this.shuffleArray(frags);
        container.innerHTML = `
            <div class="exercice-type-badge">🔀 Remets en ordre</div>
            ${this.streakHtml()}
            <div class="exercice-question">${Francophile.escapeHtml(ex.question)}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            <div class="drag-zone drag-source" id="drag-source">
                ${frags.map((f,i) => `<div class="drag-item" draggable="true" data-id="${i}" data-value="${Francophile.escapeHtml(f)}">${Francophile.escapeHtml(f)}</div>`).join('')}
            </div>
            <div class="drag-arrow">↓ Glisse ou clique pour ordonner ↓</div>
            <div class="drag-zone drag-target" id="drag-target"></div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-outline btn-small" id="btn-reset-drag">↺ Reset</button>
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        this.setupDragZones('#drag-source', '#drag-target');
        Francophile.$('#btn-reset-drag')?.addEventListener('click', () => {
            Francophile.$$('#drag-target .drag-item').forEach(i => Francophile.$('#drag-source').appendChild(i));
        });
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            const items = Francophile.$$('#drag-target .drag-item');
            if (!items.length) return Francophile.notify('Glisse les mots dans la zone', 'warning');
            this.submitAnswer([...items].map(i => i.dataset.value).join(' '));
        });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: DRAG CATEGORY (classer) ----
    renderDragCategory(container, ex) {
        const items = [...ex.items];
        this.shuffleArray(items);
        const cats = Object.keys(ex.categories);
        container.innerHTML = `
            <div class="exercice-type-badge">📂 Classe les mots</div>
            ${this.streakHtml()}
            <div class="exercice-question">${Francophile.escapeHtml(ex.question)}</div>
            <div class="drag-zone drag-source" id="drag-source">
                ${items.map((it,i) => `<div class="drag-item" draggable="true" data-id="${i}" data-value="${Francophile.escapeHtml(it)}">${Francophile.escapeHtml(it)}</div>`).join('')}
            </div>
            <div class="category-targets">
                ${cats.map(c => `<div class="category-box"><div class="category-label">${Francophile.escapeHtml(c)}</div><div class="drag-zone drag-target category-drop" data-category="${Francophile.escapeHtml(c)}"></div></div>`).join('')}
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-outline btn-small" id="btn-reset-drag">↺ Reset</button>
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        this.setupDragMultiZone();
        Francophile.$('#btn-reset-drag')?.addEventListener('click', () => {
            Francophile.$$('.category-drop .drag-item').forEach(i => Francophile.$('#drag-source').appendChild(i));
        });
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            if (Francophile.$$('#drag-source .drag-item').length) return Francophile.notify('Place tous les mots', 'warning');
            const answer = {};
            Francophile.$$('.category-drop').forEach(z => {
                answer[z.dataset.category] = [...z.querySelectorAll('.drag-item')].map(i => i.dataset.value);
            });
            this.submitAnswer(JSON.stringify(answer));
        });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: VRAI / FAUX ----
    renderVraiFaux(container, ex) {
        container.innerHTML = `
            <div class="exercice-type-badge">✓✗ Vrai ou Faux</div>
            ${this.streakHtml()}
            <div class="exercice-question">${Francophile.escapeHtml(ex.question)}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            <div class="vf-buttons">
                <button class="vf-btn vf-vrai" data-value="vrai"><span class="vf-icon">✓</span><span>Correct</span></button>
                <button class="vf-btn vf-faux" data-value="faux"><span class="vf-icon">✗</span><span>Incorrect</span></button>
            </div>
            <div class="vf-correction hidden" id="vf-correction-zone">
                <label class="form-label">Corrige la phrase :</label>
                <input type="text" class="form-input" id="vf-correction-input" placeholder="Écris la correction..." autocomplete="off">
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large hidden" id="btn-valider">Valider la correction</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        Francophile.$$('.vf-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                Francophile.$$('.vf-btn').forEach(b => { b.classList.remove('selected'); b.style.pointerEvents = 'none'; });
                btn.classList.add('selected');
                if (btn.dataset.value === 'faux' && ex.reponse_correcte === 'faux' && ex.correction) {
                    Francophile.$('#vf-correction-zone')?.classList.remove('hidden');
                    Francophile.$('#btn-valider')?.classList.remove('hidden');
                    Francophile.$('#vf-correction-input')?.focus();
                } else {
                    this.submitAnswer(btn.dataset.value);
                }
            });
        });
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            const c = Francophile.$('#vf-correction-input')?.value.trim();
            if (!c) return Francophile.notify('Écris ta correction', 'warning');
            this.submitAnswer('faux', c);
        });
        Francophile.$('#vf-correction-input')?.addEventListener('keypress', e => { if (e.key === 'Enter') Francophile.$('#btn-valider')?.click(); });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: TRANSFORMATION ----
    renderTransformation(container, ex) {
        container.innerHTML = `
            <div class="exercice-type-badge">🔄 Transformation</div>
            ${this.streakHtml()}
            <div class="exercice-consigne">${Francophile.escapeHtml(ex.consigne)}</div>
            <div class="exercice-question transformation-source">${Francophile.escapeHtml(ex.question)}</div>
            <div class="transformation-arrow">→</div>
            <div class="exercice-input">
                <textarea class="form-input transformation-input" id="reponse-input" placeholder="Écris la phrase transformée..." rows="2" autocomplete="off" spellcheck="false"></textarea>
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        const inp = Francophile.$('#reponse-input');
        inp?.focus();
        inp?.addEventListener('keypress', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.submitInputAnswer(); } });
        Francophile.$('#btn-valider')?.addEventListener('click', () => this.submitInputAnswer());
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: INTRUS ----
    renderIntrus(container, ex) {
        const items = [...ex.items];
        this.shuffleArray(items);
        container.innerHTML = `
            <div class="exercice-type-badge">🔍 Trouve l'intrus</div>
            ${this.streakHtml()}
            <div class="exercice-question">${Francophile.escapeHtml(ex.question)}</div>
            ${ex.indication ? `<div class="exercice-indication">${Francophile.escapeHtml(ex.indication)}</div>` : ''}
            <div class="intrus-grid">
                ${items.map(it => `<button class="intrus-item" data-value="${Francophile.escapeHtml(it)}">${Francophile.escapeHtml(it)}</button>`).join('')}
            </div>
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        Francophile.$$('.intrus-item').forEach(it => {
            it.addEventListener('click', () => {
                Francophile.$$('.intrus-item').forEach(i => i.classList.remove('selected'));
                it.classList.add('selected');
            });
        });
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            const sel = Francophile.$('.intrus-item.selected');
            if (!sel) return Francophile.notify('Sélectionne l\'intrus', 'warning');
            this.submitAnswer(sel.dataset.value);
        });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    // ---- FORMAT: MULTI-TROUS ----
    renderMultiTrous(container, ex) {
        let html = Francophile.escapeHtml(ex.texte);
        ex.trous.forEach(t => {
            html = html.replace(`{${t.id}}`, `<span class="multi-trou-slot" data-id="${t.id}"><input type="text" class="multi-trou-input" data-id="${t.id}" placeholder="..." autocomplete="off" spellcheck="false"></span>`);
        });
        container.innerHTML = `
            <div class="exercice-type-badge">📝 Texte à trous</div>
            ${this.streakHtml()}
            ${ex.consigne ? `<div class="exercice-consigne">${Francophile.escapeHtml(ex.consigne)}</div>` : ''}
            <div class="multi-trous-text">${html}</div>
            ${ex.options ? `<div class="multi-trous-wordbank"><div class="wordbank-label">Mots disponibles :</div><div class="wordbank-items">${ex.options.map(w => `<span class="wordbank-item">${Francophile.escapeHtml(w)}</span>`).join('')}</div></div>` : ''}
            ${this.timerHtml()}
            <div class="exercice-feedback hidden"></div>
            <div class="exercice-actions">
                <button class="btn btn-primary btn-large" id="btn-valider">Valider</button>
                <button class="btn btn-outline btn-large hidden" id="btn-suivant">Suivant →</button>
            </div>`;
        const inputs = [...Francophile.$$('.multi-trou-input')];
        inputs.forEach((inp, idx) => {
            inp.addEventListener('keypress', e => {
                if (e.key === 'Enter') { e.preventDefault(); idx < inputs.length - 1 ? inputs[idx+1].focus() : Francophile.$('#btn-valider')?.click(); }
            });
        });
        if (inputs[0]) inputs[0].focus();
        Francophile.$('#btn-valider')?.addEventListener('click', () => {
            const answers = {};
            let allFilled = true;
            Francophile.$$('.multi-trou-input').forEach(inp => { const v = inp.value.trim(); if (!v) allFilled = false; answers[inp.dataset.id] = v; });
            if (!allFilled) return Francophile.notify('Remplis tous les trous', 'warning');
            this.submitMultiTrous(answers, ex);
        });
        Francophile.$('#btn-suivant')?.addEventListener('click', () => this.suivant());
    },

    submitMultiTrous(answers, ex) {
        let allCorrect = true, nbCorrect = 0;
        ex.trous.forEach(t => {
            const user = (answers[t.id] || '').toLowerCase().trim();
            const correct = t.reponse.toLowerCase().trim();
            const inp = Francophile.$(`.multi-trou-input[data-id="${t.id}"]`);
            const slot = Francophile.$(`.multi-trou-slot[data-id="${t.id}"]`);
            if (user === correct) { nbCorrect++; slot?.classList.add('correct'); }
            else { allCorrect = false; slot?.classList.add('incorrect'); const sp = document.createElement('span'); sp.className = 'trou-correction'; sp.textContent = t.reponse; slot?.appendChild(sp); }
            if (inp) inp.disabled = true;
        });
        if (allCorrect) this.score++;
        this.updateStreak(allCorrect);
        this.afficherFeedback(allCorrect, allCorrect ? '' : `${nbCorrect}/${ex.trous.length} correct${nbCorrect > 1 ? 's' : ''}`, ex.explication);
        this.saveAnswer(allCorrect, JSON.stringify(answers));
        Francophile.$('#btn-valider')?.classList.add('hidden');
        Francophile.$('#btn-suivant')?.classList.remove('hidden');
    },

    // ============================================
    // ANSWER VALIDATION
    // ============================================
    submitAnswer(reponse, correction = null) {
        const ex = this.exercices[this.currentIndex];
        this.stopTimer();
        let correct = false;
        const fmt = ex.format || 'qcm';

        if (fmt === 'vrai_faux') {
            correct = reponse.toLowerCase() === ex.reponse_correcte.toLowerCase();
            if (correct && correction && ex.correction) {
                correct = correction.toLowerCase().trim().replace(/\s+/g,' ') === ex.correction.toLowerCase().trim().replace(/\s+/g,' ');
            }
        } else if (fmt === 'drag_category') {
            try {
                const ua = JSON.parse(reponse);
                correct = true;
                for (const cat of Object.keys(ex.categories)) {
                    const exp = ex.categories[cat].map(s => s.toLowerCase()).sort();
                    const usr = (ua[cat]||[]).map(s => s.toLowerCase()).sort();
                    if (JSON.stringify(exp) !== JSON.stringify(usr)) { correct = false; break; }
                }
            } catch { correct = false; }
        } else {
            const norm = s => s.toLowerCase().trim().replace(/\s+/g,' ').replace(/['']/g, "'");
            correct = norm(reponse) === norm(ex.reponse_correcte);
        }

        if (correct) this.score++;
        this.updateStreak(correct);
        this.showCorrectIncorrect(correct, ex, fmt);
        this.afficherFeedback(correct, ex.reponse_correcte, ex.explication, correction && !correct ? ex.correction : null);
        this.saveAnswer(correct, reponse);

        Francophile.$('#btn-valider')?.classList.add('hidden');
        Francophile.$('#btn-suivant')?.classList.remove('hidden');
        Francophile.$$('.exercice-option,.vf-btn,.intrus-item').forEach(o => o.style.pointerEvents = 'none');
        Francophile.$$('.drag-item').forEach(d => { d.draggable = false; d.style.pointerEvents = 'none'; });
    },

    showCorrectIncorrect(correct, ex, fmt) {
        const mark = (sel, cls) => Francophile.$$(sel).forEach(o => {
            if (o.dataset.value?.toLowerCase() === ex.reponse_correcte?.toLowerCase()) o.classList.add('correct');
            else if (o.classList.contains('selected') && !correct) o.classList.add('incorrect');
        });
        if (fmt === 'qcm') mark('.exercice-option');
        else if (fmt === 'intrus') mark('.intrus-item');
        else if (fmt === 'vrai_faux') Francophile.$$('.vf-btn').forEach(b => {
            if (b.dataset.value === ex.reponse_correcte) b.classList.add('correct');
            else if (b.classList.contains('selected')) b.classList.add('incorrect');
        });
    },

    // ---- FEEDBACK & STREAK ----
    afficherFeedback(correct, reponseCorrecte, explication, correctionText = null) {
        const fb = Francophile.$('.exercice-feedback');
        if (!fb) return;
        fb.classList.remove('hidden', 'correct', 'incorrect');
        fb.classList.add(correct ? 'correct' : 'incorrect');
        const streakMsg = correct && this.streak >= 3 ? `<div class="streak-msg">🔥 Série de ${this.streak} !</div>` : '';
        fb.innerHTML = `
            <div class="feedback-icon">${correct ? '✓' : '✗'}</div>
            <div class="feedback-text">${correct ? 'Bravo !' : 'Pas tout à fait...'}</div>
            ${!correct && reponseCorrecte ? `<div class="feedback-answer">Réponse : <strong>${Francophile.escapeHtml(String(reponseCorrecte))}</strong></div>` : ''}
            ${correctionText ? `<div class="feedback-answer">Correction : <strong>${Francophile.escapeHtml(correctionText)}</strong></div>` : ''}
            ${explication ? `<div class="feedback-explanation">${Francophile.escapeHtml(explication)}</div>` : ''}
            ${streakMsg}`;
    },

    updateStreak(correct) {
        if (correct) { this.streak++; if (this.streak > this.bestStreak) this.bestStreak = this.streak; }
        else this.streak = 0;
        const el = Francophile.$('.streak-display');
        if (el) { if (this.streak >= 2) { el.textContent = `🔥 ${this.streak}`; el.classList.add('active'); } else el.classList.remove('active'); }
    },

    streakHtml() {
        return `<div class="streak-display ${this.streak >= 2 ? 'active' : ''}">${this.streak >= 2 ? '🔥 ' + this.streak : ''}</div>`;
    },

    // ---- TIMER ----
    timerHtml() { return this.timerEnabled ? `<div class="exercice-timer"><div class="timer-bar" id="timer-bar"></div></div>` : ''; },
    startTimer() {
        if (!this.timerEnabled) return;
        const bar = Francophile.$('#timer-bar'); if (!bar) return;
        let rem = this.timerLimit;
        bar.style.width = '100%'; bar.classList.remove('timer-warning','timer-danger');
        this.timerInterval = setInterval(() => {
            rem -= 0.1; const pct = (rem / this.timerLimit) * 100;
            bar.style.width = pct + '%';
            if (pct < 30) bar.classList.add('timer-danger'); else if (pct < 60) bar.classList.add('timer-warning');
            if (rem <= 0) { this.stopTimer(); Francophile.notify('Temps écoulé !', 'error'); this.submitAnswer('__TIMEOUT__'); }
        }, 100);
    },
    stopTimer() { if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; } },

    // ---- DRAG HELPERS ----
    setupDragZones(sourceId, targetId) {
        const src = Francophile.$(sourceId), tgt = Francophile.$(targetId);
        let dragged = null;
        Francophile.$$(`${sourceId} .drag-item, ${targetId} .drag-item`).forEach(item => {
            item.addEventListener('click', () => {
                const p = item.parentElement;
                (p === src ? tgt : src).appendChild(item);
                item.classList.add('drag-bounce'); setTimeout(() => item.classList.remove('drag-bounce'), 300);
            });
            item.addEventListener('dragstart', () => { dragged = item; item.classList.add('dragging'); });
            item.addEventListener('dragend', () => { item.classList.remove('dragging'); dragged = null; });
        });
        [src, tgt].forEach(zone => {
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); if (dragged && zone === tgt) { const after = this.getDragAfter(zone, e.clientX); after ? zone.insertBefore(dragged, after) : zone.appendChild(dragged); } });
            zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
            zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag-over'); if (dragged) zone.appendChild(dragged); });
        });
    },

    setupDragMultiZone() {
        const src = Francophile.$('#drag-source');
        let dragged = null;
        Francophile.$$('.drag-item').forEach(item => {
            item.addEventListener('dragstart', () => { dragged = item; item.classList.add('dragging'); });
            item.addEventListener('dragend', () => { item.classList.remove('dragging'); dragged = null; });
            item.addEventListener('click', () => {
                const targets = [...Francophile.$$('.category-drop')];
                if (item.parentElement === src) targets[0]?.appendChild(item);
                else { const idx = targets.indexOf(item.parentElement) + 1; (idx >= targets.length ? src : targets[idx]).appendChild(item); }
                item.classList.add('drag-bounce'); setTimeout(() => item.classList.remove('drag-bounce'), 300);
            });
        });
        Francophile.$$('.category-drop').forEach(z => {
            z.addEventListener('dragover', e => { e.preventDefault(); z.classList.add('drag-over'); });
            z.addEventListener('dragleave', () => z.classList.remove('drag-over'));
            z.addEventListener('drop', e => { e.preventDefault(); z.classList.remove('drag-over'); if (dragged) z.appendChild(dragged); });
        });
        src.addEventListener('dragover', e => { e.preventDefault(); src.classList.add('drag-over'); });
        src.addEventListener('dragleave', () => src.classList.remove('drag-over'));
        src.addEventListener('drop', e => { e.preventDefault(); src.classList.remove('drag-over'); if (dragged) src.appendChild(dragged); });
    },

    getDragAfter(container, x) {
        return [...container.querySelectorAll('.drag-item:not(.dragging)')].reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = x - box.left - box.width / 2;
            return offset < 0 && offset > closest.offset ? { offset, element: child } : closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    },

    // ---- SAVE / NEXT / FINISH ----
    async saveAnswer(correct, reponse) {
        const ex = this.exercices[this.currentIndex];
        try { await Francophile.post('/save_reponse.php', {
            session_id: this.sessionId, exercice_type: ex.type || ex.format || 'unknown',
            exercice_id: ex.id || 0, question: ex.question || ex.texte || '',
            reponse_attendue: ex.reponse_correcte || '', reponse_donnee: reponse, correct,
            temps_reponse: Math.round((Date.now() - this.questionStartTime) / 1000)
        }); } catch (e) { console.error('Save:', e); }
    },

    suivant() {
        this.currentIndex++;
        if (this.currentIndex >= this.exercices.length) this.terminer();
        else { this.afficherExercice(); this.updateProgress(); }
    },

    updateProgress() {
        const c = Francophile.$('.exercice-counter');
        if (c) c.textContent = `${this.currentIndex + 1} / ${this.exercices.length}`;
        const b = Francophile.$('.progress-bar');
        if (b) b.style.width = `${(this.currentIndex / this.exercices.length) * 100}%`;
    },

    async terminer() {
        this.stopTimer();
        try { const r = await Francophile.post('/terminer_session.php', { session_id: this.sessionId }); this.afficherResultats(r); }
        catch (e) { console.error(e); this.afficherResultats({}); }
    },

    afficherResultats(result) {
        const container = Francophile.$('.exercice-container');
        const taux = result.taux_reussite || Math.round((this.score / this.exercices.length) * 100);
        const pts = result.points_nets || 0;
        let emoji = '😕'; if (taux >= 90) emoji = '🏆'; else if (taux >= 70) emoji = '😊'; else if (taux >= 50) emoji = '🙂';
        container.innerHTML = `
            <div class="results-card">
                <div class="results-emoji score-pop">${emoji}</div>
                <h2 class="results-title">Session terminée !</h2>
                <div class="results-score score-pop" style="color:${taux >= 70 ? 'var(--success)' : taux >= 50 ? 'var(--warning)' : 'var(--error)'}">${taux}%</div>
                <div class="results-details">
                    <div class="results-detail"><span>✅</span> <strong>${this.score}</strong> / ${this.exercices.length}</div>
                    <div class="results-detail"><span>⚡</span> <strong>${pts >= 0 ? '+' : ''}${pts}</strong> points</div>
                    ${this.bestStreak >= 3 ? `<div class="results-detail"><span>🔥</span> Meilleure série : <strong>${this.bestStreak}</strong></div>` : ''}
                </div>
                ${result.progression?.level_up ? '<div class="results-levelup">🎉 Niveau supérieur !</div>' : ''}
                <div class="results-actions">
                    <a href="/eleve/exercices.php" class="btn btn-outline">Autres exercices</a>
                    <a href="/eleve/dashboard.php" class="btn btn-primary">Tableau de bord</a>
                </div>
            </div>`;
    },

    shuffleArray(arr) { for (let i = arr.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [arr[i], arr[j]] = [arr[j], arr[i]]; } return arr; }
};

const ClasseManager = { copyCode(code) { navigator.clipboard.writeText(code).then(() => Francophile.notify('Code copié !', 'success')); } };
window.Francophile = Francophile;
window.ExerciceManager = ExerciceManager;
window.ClasseManager = ClasseManager;
