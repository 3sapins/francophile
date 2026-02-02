<?php
/**
 * Classe Conjugueur - Génération automatique des conjugaisons françaises
 */
class Conjugueur {
    
    private PDO $db;
    private array $irreguliers = [];
    private array $personnes = ['je', 'tu', 'il', 'nous', 'vous', 'ils'];
    private array $personnesImperatif = ['tu', 'nous', 'vous'];
    
    private array $terminaisons = [
        '1' => [
            'present' => ['e', 'es', 'e', 'ons', 'ez', 'ent'],
            'imparfait' => ['ais', 'ais', 'ait', 'ions', 'iez', 'aient'],
            'passe_simple' => ['ai', 'as', 'a', 'âmes', 'âtes', 'èrent'],
            'futur_simple' => ['erai', 'eras', 'era', 'erons', 'erez', 'eront'],
            'conditionnel_present' => ['erais', 'erais', 'erait', 'erions', 'eriez', 'eraient'],
            'subjonctif_present' => ['e', 'es', 'e', 'ions', 'iez', 'ent'],
            'imperatif_present' => ['e', 'ons', 'ez'],
            'participe_passe' => 'é'
        ],
        '2' => [
            'present' => ['is', 'is', 'it', 'issons', 'issez', 'issent'],
            'imparfait' => ['issais', 'issais', 'issait', 'issions', 'issiez', 'issaient'],
            'passe_simple' => ['is', 'is', 'it', 'îmes', 'îtes', 'irent'],
            'futur_simple' => ['irai', 'iras', 'ira', 'irons', 'irez', 'iront'],
            'conditionnel_present' => ['irais', 'irais', 'irait', 'irions', 'iriez', 'iraient'],
            'subjonctif_present' => ['isse', 'isses', 'isse', 'issions', 'issiez', 'issent'],
            'imperatif_present' => ['is', 'issons', 'issez'],
            'participe_passe' => 'i'
        ]
    ];
    
    private array $verbes2eGroupe = [
        'finir', 'choisir', 'réussir', 'grandir', 'grossir', 'maigrir', 'rougir', 
        'obéir', 'réfléchir', 'agir', 'bâtir', 'remplir', 'salir', 'accomplir', 
        'applaudir', 'avertir', 'bannir', 'définir', 'démolir', 'éblouir', 
        'éclaircir', 'élargir', 'embellir', 'enrichir', 'envahir', 'établir', 
        'garantir', 'guérir', 'investir', 'nourrir', 'punir', 'rafraîchir', 
        'réagir', 'réunir', 'saisir', 'subir', 'trahir', 'unir', 'vieillir',
        'abolir', 'aboutir', 'adoucir', 'affaiblir', 'agrandir', 'alourdir',
        'amincir', 'anéantir', 'approfondir', 'arrondir', 'assainir', 'assombrir',
        'avilir', 'blanchir', 'blêmir', 'bondir', 'brunir', 'compatir', 'convertir',
        'dégourdir', 'désobéir', 'divertir', 'durcir', 'ébahir', 'épaissir',
        'épanouir', 'faiblir', 'fleurir', 'fléchir', 'fournir', 'fraîchir',
        'franchir', 'frémir', 'gémir', 'gravir', 'jaillir', 'jaunir', 'jouir',
        'languir', 'mûrir', 'noircir', 'pâlir', 'périr', 'polir', 'pourrir',
        'raccourcir', 'raidir', 'rajeunir', 'ralentir', 'ramollir', 'ravir',
        'rebondir', 'refroidir', 'rejaillir', 'resplendir', 'retentir', 'rétrécir',
        'rôtir', 'roussir', 'rugir', 'ternir', 'tiédir', 'verdir', 'vernir', 'vomir'
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->chargerIrreguliers();
    }
    
    private function chargerIrreguliers(): void {
        $fichier = __DIR__ . '/../data/verbes_irreguliers.php';
        if (file_exists($fichier)) {
            $this->irreguliers = require $fichier;
        }
    }
    
    public function conjuguer(string $infinitif, string $temps, string $personne): ?string {
        $infinitif = mb_strtolower(trim($infinitif));
        
        // Verbes pronominaux
        $pronominal = false;
        $infinitifBase = $infinitif;
        if (preg_match('/^se?\s+(.+)$/', $infinitif, $matches)) {
            $pronominal = true;
            $infinitifBase = $matches[1];
        }
        
        // Chercher dans les irréguliers
        if (isset($this->irreguliers[$infinitifBase])) {
            $forme = $this->conjuguerIrregulier($infinitifBase, $temps, $personne);
            if ($forme !== null && $pronominal && $temps !== 'participe_passe') {
                return $this->ajouterPronom($forme, $personne);
            }
            return $forme;
        }
        
        // Chercher les composés
        $verbeBase = $this->trouverVerbeBase($infinitifBase);
        if ($verbeBase && isset($this->irreguliers[$verbeBase])) {
            $prefixe = substr($infinitifBase, 0, strlen($infinitifBase) - strlen($verbeBase));
            $forme = $this->conjuguerIrregulier($verbeBase, $temps, $personne);
            if ($forme !== null) {
                $formeComplete = $prefixe . $forme;
                if ($pronominal && $temps !== 'participe_passe') {
                    return $this->ajouterPronom($formeComplete, $personne);
                }
                return $formeComplete;
            }
        }
        
        // Régulier
        $forme = $this->conjuguerRegulier($infinitifBase, $temps, $personne);
        if ($forme !== null && $pronominal && $temps !== 'participe_passe') {
            return $this->ajouterPronom($forme, $personne);
        }
        return $forme;
    }
    
    private function trouverVerbeBase(string $infinitif): ?string {
        $bases = ['venir', 'tenir', 'prendre', 'mettre', 'faire', 'dire', 'voir', 
            'écrire', 'lire', 'duire', 'battre', 'naître', 'paraître', 'connaître',
            'courir', 'ouvrir', 'cueillir', 'partir', 'sortir', 'dormir', 'servir',
            'croire', 'boire', 'plaire', 'vivre', 'suivre', 'rire', 'conduire',
            'craindre', 'peindre', 'joindre', 'vaincre', 'résoudre', 'coudre',
            'moudre', 'conclure', 'rompre', 'cevoir'];
        
        foreach ($bases as $base) {
            if (strlen($infinitif) > strlen($base) && str_ends_with($infinitif, $base)) {
                return $base;
            }
        }
        return null;
    }
    
    private function conjuguerIrregulier(string $infinitif, string $temps, string $personne): ?string {
        if (!isset($this->irreguliers[$infinitif][$temps])) return null;
        
        $formes = $this->irreguliers[$infinitif][$temps];
        if ($formes === null) return null;
        if (is_string($formes)) return $formes;
        
        if ($temps === 'imperatif_present') {
            $index = array_search($personne, $this->personnesImperatif);
        } else {
            $index = array_search($personne, $this->personnes);
        }
        
        return ($index !== false && isset($formes[$index])) ? $formes[$index] : null;
    }
    
    private function conjuguerRegulier(string $infinitif, string $temps, string $personne): ?string {
        $groupe = $this->detecterGroupe($infinitif);
        if ($groupe === '3') return null;
        
        $radical = $this->extraireRadical($infinitif, $groupe, $temps, $personne);
        $terminaison = $this->getTerminaison($groupe, $temps, $personne);
        
        if ($radical === null || $terminaison === null) return null;
        return $radical . $terminaison;
    }
    
    public function detecterGroupe(string $infinitif): string {
        if (preg_match('/er$/', $infinitif) && $infinitif !== 'aller') return '1';
        if (preg_match('/ir$/', $infinitif) && in_array($infinitif, $this->verbes2eGroupe)) return '2';
        return '3';
    }
    
    private function extraireRadical(string $infinitif, string $groupe, string $temps, string $personne): ?string {
        if ($groupe === '1') {
            $radical = preg_replace('/er$/', '', $infinitif);
            
            // -cer : c -> ç
            if (preg_match('/c$/', $radical) && in_array($temps, ['imparfait', 'passe_simple'])) {
                if ($temps === 'imparfait' && in_array($personne, ['je', 'tu', 'il', 'ils'])) {
                    $radical = substr($radical, 0, -1) . 'ç';
                }
            }
            
            // -ger : g -> ge
            if (preg_match('/g$/', $radical) && in_array($temps, ['imparfait', 'passe_simple'])) {
                if ($temps === 'imparfait' && in_array($personne, ['je', 'tu', 'il', 'ils'])) {
                    $radical .= 'e';
                }
            }
            
            // -yer : y -> i
            if (preg_match('/[aou]y$/', $radical)) {
                if (in_array($temps, ['present', 'subjonctif_present']) && in_array($personne, ['je', 'tu', 'il', 'ils'])) {
                    $radical = preg_replace('/y$/', 'i', $radical);
                }
                if (in_array($temps, ['futur_simple', 'conditionnel_present'])) {
                    $radical = preg_replace('/y$/', 'i', $radical);
                }
            }
            
            return $radical;
        }
        
        if ($groupe === '2') {
            return preg_replace('/ir$/', '', $infinitif);
        }
        
        return null;
    }
    
    private function getTerminaison(string $groupe, string $temps, string $personne): ?string {
        if (!isset($this->terminaisons[$groupe][$temps])) return null;
        
        $terminaisons = $this->terminaisons[$groupe][$temps];
        
        if ($temps === 'participe_passe') {
            return is_string($terminaisons) ? $terminaisons : $terminaisons[0];
        }
        
        if ($temps === 'imperatif_present') {
            $index = array_search($personne, $this->personnesImperatif);
        } else {
            $index = array_search($personne, $this->personnes);
        }
        
        return ($index !== false) ? $terminaisons[$index] : null;
    }
    
    private function ajouterPronom(string $forme, string $personne): string {
        $pronoms = ['je' => 'me ', 'tu' => 'te ', 'il' => 'se ', 'nous' => 'nous ', 'vous' => 'vous ', 'ils' => 'se '];
        
        if (in_array($personne, ['je', 'tu', 'il', 'ils']) && preg_match('/^[aeéèêiîoôuùûh]/i', $forme)) {
            $pronoms['je'] = "m'"; $pronoms['tu'] = "t'"; $pronoms['il'] = "s'"; $pronoms['ils'] = "s'";
        }
        
        return ($pronoms[$personne] ?? '') . $forme;
    }
    
    public function getAuxiliaire(string $infinitif): string {
        $infinitif = mb_strtolower(trim($infinitif));
        if (preg_match('/^se?\s+/', $infinitif)) return 'être';
        
        $infinitifBase = preg_replace('/^se?\s+/', '', $infinitif);
        if (isset($this->irreguliers[$infinitifBase]['auxiliaire'])) {
            return $this->irreguliers[$infinitifBase]['auxiliaire'];
        }
        
        $verbsEtre = ['aller', 'arriver', 'descendre', 'devenir', 'entrer', 'monter', 
            'mourir', 'naître', 'partir', 'passer', 'rester', 'retourner', 'sortir', 
            'tomber', 'venir', 'revenir', 'parvenir', 'intervenir', 'survenir'];
        
        return in_array($infinitifBase, $verbsEtre) ? 'être' : 'avoir';
    }
    
    public function getParticipePasse(string $infinitif): ?string {
        $infinitif = mb_strtolower(trim($infinitif));
        $infinitifBase = preg_replace('/^se?\s+/', '', $infinitif);
        
        if (isset($this->irreguliers[$infinitifBase]['participe_passe'])) {
            return $this->irreguliers[$infinitifBase]['participe_passe'];
        }
        
        $verbeBase = $this->trouverVerbeBase($infinitifBase);
        if ($verbeBase && isset($this->irreguliers[$verbeBase]['participe_passe'])) {
            $prefixe = substr($infinitifBase, 0, strlen($infinitifBase) - strlen($verbeBase));
            return $prefixe . $this->irreguliers[$verbeBase]['participe_passe'];
        }
        
        $groupe = $this->detecterGroupe($infinitifBase);
        return match($groupe) {
            '1' => preg_replace('/er$/', 'é', $infinitifBase),
            '2' => preg_replace('/ir$/', 'i', $infinitifBase),
            default => null
        };
    }
    
    public function getToutesPersonnes(string $infinitif, string $temps): array {
        $result = [];
        $personnes = ($temps === 'imperatif_present') ? $this->personnesImperatif : $this->personnes;
        
        foreach ($personnes as $personne) {
            $forme = $this->conjuguer($infinitif, $temps, $personne);
            if ($forme !== null) $result[$personne] = $forme;
        }
        return $result;
    }
    
    public function verifierReponse(string $infinitif, string $temps, string $personne, string $reponse): bool {
        $formeCorrecte = $this->conjuguer($infinitif, $temps, $personne);
        if ($formeCorrecte === null) return false;
        return mb_strtolower(trim($reponse)) === mb_strtolower($formeCorrecte);
    }
    
    public function getFormeCorrecte(string $infinitif, string $temps, string $personne): ?string {
        return $this->conjuguer($infinitif, $temps, $personne);
    }
}
