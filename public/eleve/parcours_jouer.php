<?php
$pageTitle = 'Parcours';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEleve();

$eleve = Eleve::findById(Session::getUserId());
$db = Database::getInstance();
$niveauId = (int)($_GET['niveau'] ?? 0);

if (!$niveauId) { header('Location: /eleve/parcours.php'); exit; }

// Récupérer le niveau et son parcours
$stmt = $db->prepare('
    SELECT pn.*, p.id as parcours_id, p.nom as parcours_nom, p.code as parcours_code, p.icone, p.couleur
    FROM parcours_niveaux pn 
    JOIN parcours p ON pn.parcours_id = p.id
    WHERE pn.id = ? AND p.actif = 1
');
$stmt->execute([$niveauId]);
$niveau = $stmt->fetch();
if (!$niveau) { header('Location: /eleve/parcours.php'); exit; }

// Vérifier que le niveau est débloqué
$stmt = $db->prepare('SELECT niveau_max_atteint FROM parcours_progression WHERE eleve_id = ? AND parcours_id = ?');
$stmt->execute([$eleve->getId(), $niveau['parcours_id']]);
$prog = $stmt->fetch();
$niveauMaxAtteint = $prog['niveau_max_atteint'] ?? 0;

if ($niveau['numero'] > $niveauMaxAtteint + 1) {
    Session::setFlash('error', 'Ce niveau est encore verrouillé.');
    header('Location: /eleve/parcours_detail.php?id=' . $niveau['parcours_id']);
    exit;
}

// Récupérer les exercices dans l'ordre
$stmt = $db->prepare('SELECT * FROM parcours_exercices WHERE niveau_id = ? ORDER BY ordre');
$stmt->execute([$niveauId]);
$exercices = $stmt->fetchAll();

// Préparer les exercices pour le JS
$exercicesJs = [];
foreach ($exercices as $ex) {
    $options = [$ex['reponse_correcte']];
    if ($ex['options_incorrectes']) {
        $incorrectes = json_decode($ex['options_incorrectes'], true);
        if (is_array($incorrectes)) $options = array_merge($options, $incorrectes);
    }
    shuffle($options);
    $exercicesJs[] = [
        'id' => $ex['id'],
        'type' => $ex['type_exercice'],
        'question' => $ex['phrase'],
        'options' => $options,
        'reponse_correcte' => $ex['reponse_correcte'],
        'explication' => $ex['explication'],
        'indice' => $ex['indice']
    ];
}

$pageTitle = $niveau['parcours_nom'] . ' — Niveau ' . $niveau['numero'];
?>

<div class="container">
    <div class="parcours-jouer-container">
        <div class="parcours-jouer-header" style="--pc: <?= htmlspecialchars($niveau['couleur']) ?>;">
            <a href="/eleve/parcours_detail.php?id=<?= $niveau['parcours_id'] ?>" class="btn btn-outline btn-small">← Quitter</a>
            <div class="parcours-jouer-info">
                <span class="parcours-jouer-titre"><?= $niveau['icone'] ?> <?= htmlspecialchars($niveau['titre']) ?></span>
                <span class="parcours-jouer-counter">1 / <?= count($exercicesJs) ?></span>
            </div>
            <div class="progress" style="width: 200px;">
                <div class="progress-bar" id="parcours-progress" style="width: 0%; background: var(--pc);"></div>
            </div>
        </div>

        <div class="parcours-jouer-card" id="parcours-card">
            <div class="text-center"><p>Chargement...</p></div>
        </div>

        <div class="parcours-jouer-lives" id="parcours-lives">
            <!-- Affichage des vies/erreurs -->
        </div>
    </div>
</div>

<style>
.parcours-jouer-container { max-width: 700px; margin: 0 auto; }
.parcours-jouer-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem; padding: 1rem; background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow); }
.parcours-jouer-info { display: flex; flex-direction: column; align-items: center; }
.parcours-jouer-titre { font-weight: 700; font-size: 0.875rem; }
.parcours-jouer-counter { font-size: 0.8125rem; color: var(--gray-500); }
.parcours-jouer-card { background: white; border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow); min-height: 300px; }
.parcours-jouer-lives { text-align: center; margin-top: 1rem; font-size: 0.875rem; color: var(--gray-500); }

.parcours-question { font-size: 1.125rem; font-weight: 600; margin-bottom: 1.5rem; line-height: 1.5; text-align: center; }
.parcours-question .blank { display: inline-block; min-width: 80px; border-bottom: 3px solid var(--primary); }
.parcours-indice { text-align: center; color: var(--gray-500); font-size: 0.8125rem; margin-bottom: 1rem; font-style: italic; }
.parcours-options { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem; }
.parcours-option { padding: 0.875rem 1rem; border: 2px solid var(--gray-200); border-radius: var(--radius-lg); background: white; cursor: pointer; font-size: 1rem; font-weight: 500; text-align: center; transition: all 0.15s; }
.parcours-option:hover { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }
.parcours-option.selected { border-color: var(--primary); background: var(--primary); color: white; }
.parcours-option.correct { border-color: var(--success); background: #dcfce7; color: #166534; }
.parcours-option.incorrect { border-color: var(--error); background: #fef2f2; color: #991b1b; }
.parcours-option.disabled { pointer-events: none; opacity: 0.7; }

.parcours-input { display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 1.5rem; }
.parcours-input input { max-width: 300px; text-align: center; font-size: 1.125rem; }

.parcours-feedback { padding: 1rem; border-radius: var(--radius-lg); margin-bottom: 1rem; text-align: center; }
.parcours-feedback.correct { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.parcours-feedback.incorrect { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.parcours-feedback-icon { font-size: 1.5rem; margin-bottom: 0.25rem; }

.parcours-actions { text-align: center; margin-top: 1rem; }

.parcours-result { text-align: center; padding: 2rem 0; }
.parcours-result-score { font-size: 3rem; font-weight: 800; margin: 1rem 0; }
.parcours-result-score.success { color: var(--success); }
.parcours-result-score.fail { color: var(--error); }
.parcours-result-detail { margin: 1rem 0; }
.parcours-result-errors { background: #fef2f2; border-radius: var(--radius-lg); padding: 1rem; margin: 1rem 0; text-align: left; }
.parcours-result-errors h4 { color: #991b1b; margin-bottom: 0.5rem; }
.parcours-result-error-item { padding: 0.5rem 0; border-bottom: 1px solid #fecaca; font-size: 0.875rem; }
.parcours-result-error-item:last-child { border-bottom: none; }

@media (max-width: 640px) {
    .parcours-options { grid-template-columns: 1fr; }
    .parcours-jouer-header { flex-direction: column; }
}
</style>

<script>
const ParcoursPlayer = {
    exercices: <?= json_encode($exercicesJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
    niveauId: <?= $niveauId ?>,
    parcoursId: <?= $niveau['parcours_id'] ?>,
    currentIndex: 0,
    score: 0,
    erreurs: [],

    init() {
        if (this.exercices.length === 0) return;
        this.afficher();
    },

    afficher() {
        const ex = this.exercices[this.currentIndex];
        const card = document.getElementById('parcours-card');
        const counter = document.querySelector('.parcours-jouer-counter');
        const progressBar = document.getElementById('parcours-progress');

        if (counter) counter.textContent = `${this.currentIndex + 1} / ${this.exercices.length}`;
        if (progressBar) progressBar.style.width = `${(this.currentIndex / this.exercices.length) * 100}%`;

        let question = ex.question.replace('___', '<span class="blank"></span>');
        let inputHtml = '';

        if (ex.options && ex.options.length > 0) {
            inputHtml = '<div class="parcours-options">' +
                ex.options.map(o => `<button class="parcours-option" data-value="${this.escapeHtml(o)}">${this.escapeHtml(o)}</button>`).join('') +
                '</div>';
        } else {
            inputHtml = '<div class="parcours-input"><input type="text" class="form-input" id="parcours-reponse" placeholder="Ta réponse..." autofocus></div>';
        }

        card.innerHTML = `
            <div class="parcours-question">${question}</div>
            ${ex.indice ? `<div class="parcours-indice">💡 ${this.escapeHtml(ex.indice)}</div>` : ''}
            ${inputHtml}
            <div id="parcours-feedback" style="display:none;"></div>
            <div class="parcours-actions">
                <button class="btn btn-primary btn-large" id="btn-valider" style="min-width: 200px;">Valider</button>
                <button class="btn btn-primary btn-large" id="btn-suivant" style="display:none; min-width: 200px;">Suivant →</button>
            </div>
        `;

        // Events
        document.querySelectorAll('.parcours-option').forEach(opt => {
            opt.addEventListener('click', () => {
                document.querySelectorAll('.parcours-option').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });

        document.getElementById('btn-valider').addEventListener('click', () => this.valider());
        document.getElementById('btn-suivant').addEventListener('click', () => this.suivant());

        const input = document.getElementById('parcours-reponse');
        if (input) {
            input.focus();
            input.addEventListener('keypress', e => { if (e.key === 'Enter') this.valider(); });
        }

        // Mise à jour du compteur d'erreurs
        this.updateLives();
    },

    valider() {
        const ex = this.exercices[this.currentIndex];
        const selected = document.querySelector('.parcours-option.selected');
        const input = document.getElementById('parcours-reponse');
        const reponse = selected?.dataset.value || input?.value.trim() || '';

        if (!reponse) return;

        const correct = reponse.toLowerCase().trim() === ex.reponse_correcte.toLowerCase().trim();
        if (correct) this.score++;
        else this.erreurs.push({ question: ex.question, donnee: reponse, correcte: ex.reponse_correcte, explication: ex.explication });

        // Feedback
        const fb = document.getElementById('parcours-feedback');
        fb.style.display = 'block';
        fb.className = `parcours-feedback ${correct ? 'correct' : 'incorrect'}`;
        fb.innerHTML = `
            <div class="parcours-feedback-icon">${correct ? '✓' : '✗'}</div>
            <div><strong>${correct ? 'Correct !' : 'Incorrect'}</strong></div>
            ${!correct ? `<div>Réponse : <strong>${this.escapeHtml(ex.reponse_correcte)}</strong></div>` : ''}
            ${ex.explication ? `<div style="margin-top:0.5rem; font-size:0.875rem; opacity:0.8;">${this.escapeHtml(ex.explication)}</div>` : ''}
        `;

        // Marquer les options
        document.querySelectorAll('.parcours-option').forEach(o => {
            o.classList.add('disabled');
            if (o.dataset.value.toLowerCase() === ex.reponse_correcte.toLowerCase()) o.classList.add('correct');
            else if (o.classList.contains('selected') && !correct) o.classList.add('incorrect');
        });
        if (input) input.disabled = true;

        document.getElementById('btn-valider').style.display = 'none';
        document.getElementById('btn-suivant').style.display = 'inline-block';
        document.getElementById('btn-suivant').focus();

        this.updateLives();
    },

    suivant() {
        this.currentIndex++;
        if (this.currentIndex >= this.exercices.length) this.terminer();
        else this.afficher();
    },

    updateLives() {
        const lives = document.getElementById('parcours-lives');
        const nbErreurs = this.erreurs.length;
        if (nbErreurs === 0) {
            lives.innerHTML = `<span style="color: var(--success);">✓ Aucune erreur pour l'instant</span>`;
        } else {
            lives.innerHTML = `<span style="color: var(--error);">${nbErreurs} erreur${nbErreurs > 1 ? 's' : ''} — il faut 100% pour valider ce niveau</span>`;
        }
    },

    async terminer() {
        const total = this.exercices.length;
        const reussi = (this.score === total);

        // Sauvegarder la tentative
        try {
            const result = await fetch('/api/parcours_terminer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    niveau_id: this.niveauId,
                    parcours_id: this.parcoursId,
                    score: this.score,
                    total: total,
                    reussi: reussi,
                    erreurs: this.erreurs
                })
            });
            var data = await result.json();
        } catch(e) {
            var data = { success: false };
        }

        const card = document.getElementById('parcours-card');
        const lives = document.getElementById('parcours-lives');
        lives.innerHTML = '';

        if (reussi) {
            card.innerHTML = `
                <div class="parcours-result">
                    <div style="font-size: 4rem;">🎉</div>
                    <h2>Niveau réussi !</h2>
                    <div class="parcours-result-score success">${this.score}/${total}</div>
                    <p>Bravo, tu as tout juste ! +${data.points_gagnes || 0} points</p>
                    ${data.parcours_termine ? '<p style="font-size:1.25rem; margin-top:1rem;">🏆 <strong>Parcours terminé !</strong></p>' : '<p style="color:var(--success);">Le niveau suivant est débloqué !</p>'}
                    <div class="mt-lg">
                        <a href="/eleve/parcours_detail.php?id=${this.parcoursId}" class="btn btn-primary">Continuer →</a>
                    </div>
                </div>
            `;
        } else {
            let errorsHtml = this.erreurs.map(e =>
                `<div class="parcours-result-error-item">
                    <div>${e.question.replace('___', '<strong>___</strong>')}</div>
                    <div>Ta réponse : <span style="color:var(--error);">${this.escapeHtml(e.donnee)}</span> → Attendu : <strong>${this.escapeHtml(e.correcte)}</strong></div>
                    ${e.explication ? `<div style="font-size:0.8125rem; color:var(--gray-500); margin-top:0.25rem;">${this.escapeHtml(e.explication)}</div>` : ''}
                </div>`
            ).join('');

            card.innerHTML = `
                <div class="parcours-result">
                    <div style="font-size: 3rem;">😔</div>
                    <h2>Pas encore...</h2>
                    <div class="parcours-result-score fail">${this.score}/${total}</div>
                    <p>Il faut <strong>tout juste</strong> pour passer au niveau suivant.</p>
                    <div class="parcours-result-errors">
                        <h4>Tes erreurs :</h4>
                        ${errorsHtml}
                    </div>
                    <div class="mt-lg" style="display: flex; gap: 0.75rem; justify-content: center;">
                        <a href="/eleve/parcours_jouer.php?niveau=${this.niveauId}" class="btn btn-primary">Réessayer</a>
                        <a href="/eleve/parcours_detail.php?id=${this.parcoursId}" class="btn btn-outline">Retour</a>
                    </div>
                </div>
            `;
        }
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

document.addEventListener('DOMContentLoaded', () => ParcoursPlayer.init());
</script>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
