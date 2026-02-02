<?php
/**
 * FRANCOPHILE.CH - Verbes irréguliers du 3e groupe
 * Conjugaisons complètes pour le PER (9e-11e)
 * 
 * Ce fichier est inclus par la classe Conjugueur
 */

return [
    // ==========================================
    // AUXILIAIRES ET SEMI-AUXILIAIRES
    // ==========================================
    
    'être' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['suis', 'es', 'est', 'sommes', 'êtes', 'sont'],
        'imparfait' => ['étais', 'étais', 'était', 'étions', 'étiez', 'étaient'],
        'passe_simple' => ['fus', 'fus', 'fut', 'fûmes', 'fûtes', 'furent'],
        'futur_simple' => ['serai', 'seras', 'sera', 'serons', 'serez', 'seront'],
        'conditionnel_present' => ['serais', 'serais', 'serait', 'serions', 'seriez', 'seraient'],
        'subjonctif_present' => ['sois', 'sois', 'soit', 'soyons', 'soyez', 'soient'],
        'subjonctif_imparfait' => ['fusse', 'fusses', 'fût', 'fussions', 'fussiez', 'fussent'],
        'imperatif_present' => ['sois', 'soyons', 'soyez'],
        'participe_present' => 'étant',
        'participe_passe' => 'été'
    ],
    
    'avoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['ai', 'as', 'a', 'avons', 'avez', 'ont'],
        'imparfait' => ['avais', 'avais', 'avait', 'avions', 'aviez', 'avaient'],
        'passe_simple' => ['eus', 'eus', 'eut', 'eûmes', 'eûtes', 'eurent'],
        'futur_simple' => ['aurai', 'auras', 'aura', 'aurons', 'aurez', 'auront'],
        'conditionnel_present' => ['aurais', 'aurais', 'aurait', 'aurions', 'auriez', 'auraient'],
        'subjonctif_present' => ['aie', 'aies', 'ait', 'ayons', 'ayez', 'aient'],
        'subjonctif_imparfait' => ['eusse', 'eusses', 'eût', 'eussions', 'eussiez', 'eussent'],
        'imperatif_present' => ['aie', 'ayons', 'ayez'],
        'participe_present' => 'ayant',
        'participe_passe' => 'eu'
    ],
    
    'aller' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['vais', 'vas', 'va', 'allons', 'allez', 'vont'],
        'imparfait' => ['allais', 'allais', 'allait', 'allions', 'alliez', 'allaient'],
        'passe_simple' => ['allai', 'allas', 'alla', 'allâmes', 'allâtes', 'allèrent'],
        'futur_simple' => ['irai', 'iras', 'ira', 'irons', 'irez', 'iront'],
        'conditionnel_present' => ['irais', 'irais', 'irait', 'irions', 'iriez', 'iraient'],
        'subjonctif_present' => ['aille', 'ailles', 'aille', 'allions', 'alliez', 'aillent'],
        'subjonctif_imparfait' => ['allasse', 'allasses', 'allât', 'allassions', 'allassiez', 'allassent'],
        'imperatif_present' => ['va', 'allons', 'allez'],
        'participe_present' => 'allant',
        'participe_passe' => 'allé'
    ],

    // ==========================================
    // VERBES EN -IR (3e groupe)
    // ==========================================
    
    'venir' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['viens', 'viens', 'vient', 'venons', 'venez', 'viennent'],
        'imparfait' => ['venais', 'venais', 'venait', 'venions', 'veniez', 'venaient'],
        'passe_simple' => ['vins', 'vins', 'vint', 'vînmes', 'vîntes', 'vinrent'],
        'futur_simple' => ['viendrai', 'viendras', 'viendra', 'viendrons', 'viendrez', 'viendront'],
        'conditionnel_present' => ['viendrais', 'viendrais', 'viendrait', 'viendrions', 'viendriez', 'viendraient'],
        'subjonctif_present' => ['vienne', 'viennes', 'vienne', 'venions', 'veniez', 'viennent'],
        'subjonctif_imparfait' => ['vinsse', 'vinsses', 'vînt', 'vinssions', 'vinssiez', 'vinssent'],
        'imperatif_present' => ['viens', 'venons', 'venez'],
        'participe_present' => 'venant',
        'participe_passe' => 'venu'
    ],
    
    'tenir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['tiens', 'tiens', 'tient', 'tenons', 'tenez', 'tiennent'],
        'imparfait' => ['tenais', 'tenais', 'tenait', 'tenions', 'teniez', 'tenaient'],
        'passe_simple' => ['tins', 'tins', 'tint', 'tînmes', 'tîntes', 'tinrent'],
        'futur_simple' => ['tiendrai', 'tiendras', 'tiendra', 'tiendrons', 'tiendrez', 'tiendront'],
        'conditionnel_present' => ['tiendrais', 'tiendrais', 'tiendrait', 'tiendrions', 'tiendriez', 'tiendraient'],
        'subjonctif_present' => ['tienne', 'tiennes', 'tienne', 'tenions', 'teniez', 'tiennent'],
        'subjonctif_imparfait' => ['tinsse', 'tinsses', 'tînt', 'tinssions', 'tinssiez', 'tinssent'],
        'imperatif_present' => ['tiens', 'tenons', 'tenez'],
        'participe_present' => 'tenant',
        'participe_passe' => 'tenu'
    ],
    
    'partir' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['pars', 'pars', 'part', 'partons', 'partez', 'partent'],
        'imparfait' => ['partais', 'partais', 'partait', 'partions', 'partiez', 'partaient'],
        'passe_simple' => ['partis', 'partis', 'partit', 'partîmes', 'partîtes', 'partirent'],
        'futur_simple' => ['partirai', 'partiras', 'partira', 'partirons', 'partirez', 'partiront'],
        'conditionnel_present' => ['partirais', 'partirais', 'partirait', 'partirions', 'partiriez', 'partiraient'],
        'subjonctif_present' => ['parte', 'partes', 'parte', 'partions', 'partiez', 'partent'],
        'imperatif_present' => ['pars', 'partons', 'partez'],
        'participe_present' => 'partant',
        'participe_passe' => 'parti'
    ],
    
    'sortir' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['sors', 'sors', 'sort', 'sortons', 'sortez', 'sortent'],
        'imparfait' => ['sortais', 'sortais', 'sortait', 'sortions', 'sortiez', 'sortaient'],
        'passe_simple' => ['sortis', 'sortis', 'sortit', 'sortîmes', 'sortîtes', 'sortirent'],
        'futur_simple' => ['sortirai', 'sortiras', 'sortira', 'sortirons', 'sortirez', 'sortiront'],
        'conditionnel_present' => ['sortirais', 'sortirais', 'sortirait', 'sortirions', 'sortiriez', 'sortiraient'],
        'subjonctif_present' => ['sorte', 'sortes', 'sorte', 'sortions', 'sortiez', 'sortent'],
        'imperatif_present' => ['sors', 'sortons', 'sortez'],
        'participe_present' => 'sortant',
        'participe_passe' => 'sorti'
    ],
    
    'dormir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['dors', 'dors', 'dort', 'dormons', 'dormez', 'dorment'],
        'imparfait' => ['dormais', 'dormais', 'dormait', 'dormions', 'dormiez', 'dormaient'],
        'passe_simple' => ['dormis', 'dormis', 'dormit', 'dormîmes', 'dormîtes', 'dormirent'],
        'futur_simple' => ['dormirai', 'dormiras', 'dormira', 'dormirons', 'dormirez', 'dormiront'],
        'conditionnel_present' => ['dormirais', 'dormirais', 'dormirait', 'dormirions', 'dormiriez', 'dormiraient'],
        'subjonctif_present' => ['dorme', 'dormes', 'dorme', 'dormions', 'dormiez', 'dorment'],
        'imperatif_present' => ['dors', 'dormons', 'dormez'],
        'participe_present' => 'dormant',
        'participe_passe' => 'dormi'
    ],
    
    'servir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['sers', 'sers', 'sert', 'servons', 'servez', 'servent'],
        'imparfait' => ['servais', 'servais', 'servait', 'servions', 'serviez', 'servaient'],
        'passe_simple' => ['servis', 'servis', 'servit', 'servîmes', 'servîtes', 'servirent'],
        'futur_simple' => ['servirai', 'serviras', 'servira', 'servirons', 'servirez', 'serviront'],
        'conditionnel_present' => ['servirais', 'servirais', 'servirait', 'servirions', 'serviriez', 'serviraient'],
        'subjonctif_present' => ['serve', 'serves', 'serve', 'servions', 'serviez', 'servent'],
        'imperatif_present' => ['sers', 'servons', 'servez'],
        'participe_present' => 'servant',
        'participe_passe' => 'servi'
    ],
    
    'courir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['cours', 'cours', 'court', 'courons', 'courez', 'courent'],
        'imparfait' => ['courais', 'courais', 'courait', 'courions', 'couriez', 'couraient'],
        'passe_simple' => ['courus', 'courus', 'courut', 'courûmes', 'courûtes', 'coururent'],
        'futur_simple' => ['courrai', 'courras', 'courra', 'courrons', 'courrez', 'courront'],
        'conditionnel_present' => ['courrais', 'courrais', 'courrait', 'courrions', 'courriez', 'courraient'],
        'subjonctif_present' => ['coure', 'coures', 'coure', 'courions', 'couriez', 'courent'],
        'imperatif_present' => ['cours', 'courons', 'courez'],
        'participe_present' => 'courant',
        'participe_passe' => 'couru'
    ],
    
    'mourir' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['meurs', 'meurs', 'meurt', 'mourons', 'mourez', 'meurent'],
        'imparfait' => ['mourais', 'mourais', 'mourait', 'mourions', 'mouriez', 'mouraient'],
        'passe_simple' => ['mourus', 'mourus', 'mourut', 'mourûmes', 'mourûtes', 'moururent'],
        'futur_simple' => ['mourrai', 'mourras', 'mourra', 'mourrons', 'mourrez', 'mourront'],
        'conditionnel_present' => ['mourrais', 'mourrais', 'mourrait', 'mourrions', 'mourriez', 'mourraient'],
        'subjonctif_present' => ['meure', 'meures', 'meure', 'mourions', 'mouriez', 'meurent'],
        'imperatif_present' => ['meurs', 'mourons', 'mourez'],
        'participe_present' => 'mourant',
        'participe_passe' => 'mort'
    ],
    
    'acquérir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['acquiers', 'acquiers', 'acquiert', 'acquérons', 'acquérez', 'acquièrent'],
        'imparfait' => ['acquérais', 'acquérais', 'acquérait', 'acquérions', 'acquériez', 'acquéraient'],
        'passe_simple' => ['acquis', 'acquis', 'acquit', 'acquîmes', 'acquîtes', 'acquirent'],
        'futur_simple' => ['acquerrai', 'acquerras', 'acquerra', 'acquerrons', 'acquerrez', 'acquerront'],
        'conditionnel_present' => ['acquerrais', 'acquerrais', 'acquerrait', 'acquerrions', 'acquerriez', 'acquerraient'],
        'subjonctif_present' => ['acquière', 'acquières', 'acquière', 'acquérions', 'acquériez', 'acquièrent'],
        'imperatif_present' => ['acquiers', 'acquérons', 'acquérez'],
        'participe_present' => 'acquérant',
        'participe_passe' => 'acquis'
    ],
    
    'ouvrir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['ouvre', 'ouvres', 'ouvre', 'ouvrons', 'ouvrez', 'ouvrent'],
        'imparfait' => ['ouvrais', 'ouvrais', 'ouvrait', 'ouvrions', 'ouvriez', 'ouvraient'],
        'passe_simple' => ['ouvris', 'ouvris', 'ouvrit', 'ouvrîmes', 'ouvrîtes', 'ouvrirent'],
        'futur_simple' => ['ouvrirai', 'ouvriras', 'ouvrira', 'ouvrirons', 'ouvrirez', 'ouvriront'],
        'conditionnel_present' => ['ouvrirais', 'ouvrirais', 'ouvrirait', 'ouvririons', 'ouvririez', 'ouvriraient'],
        'subjonctif_present' => ['ouvre', 'ouvres', 'ouvre', 'ouvrions', 'ouvriez', 'ouvrent'],
        'imperatif_present' => ['ouvre', 'ouvrons', 'ouvrez'],
        'participe_present' => 'ouvrant',
        'participe_passe' => 'ouvert'
    ],
    
    'cueillir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['cueille', 'cueilles', 'cueille', 'cueillons', 'cueillez', 'cueillent'],
        'imparfait' => ['cueillais', 'cueillais', 'cueillait', 'cueillions', 'cueilliez', 'cueillaient'],
        'passe_simple' => ['cueillis', 'cueillis', 'cueillit', 'cueillîmes', 'cueillîtes', 'cueillirent'],
        'futur_simple' => ['cueillerai', 'cueilleras', 'cueillera', 'cueillerons', 'cueillerez', 'cueilleront'],
        'conditionnel_present' => ['cueillerais', 'cueillerais', 'cueillerait', 'cueillerions', 'cueilleriez', 'cueilleraient'],
        'subjonctif_present' => ['cueille', 'cueilles', 'cueille', 'cueillions', 'cueilliez', 'cueillent'],
        'imperatif_present' => ['cueille', 'cueillons', 'cueillez'],
        'participe_present' => 'cueillant',
        'participe_passe' => 'cueilli'
    ],

    // ==========================================
    // VERBES EN -OIR
    // ==========================================
    
    'voir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['vois', 'vois', 'voit', 'voyons', 'voyez', 'voient'],
        'imparfait' => ['voyais', 'voyais', 'voyait', 'voyions', 'voyiez', 'voyaient'],
        'passe_simple' => ['vis', 'vis', 'vit', 'vîmes', 'vîtes', 'virent'],
        'futur_simple' => ['verrai', 'verras', 'verra', 'verrons', 'verrez', 'verront'],
        'conditionnel_present' => ['verrais', 'verrais', 'verrait', 'verrions', 'verriez', 'verraient'],
        'subjonctif_present' => ['voie', 'voies', 'voie', 'voyions', 'voyiez', 'voient'],
        'imperatif_present' => ['vois', 'voyons', 'voyez'],
        'participe_present' => 'voyant',
        'participe_passe' => 'vu'
    ],
    
    'savoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['sais', 'sais', 'sait', 'savons', 'savez', 'savent'],
        'imparfait' => ['savais', 'savais', 'savait', 'savions', 'saviez', 'savaient'],
        'passe_simple' => ['sus', 'sus', 'sut', 'sûmes', 'sûtes', 'surent'],
        'futur_simple' => ['saurai', 'sauras', 'saura', 'saurons', 'saurez', 'sauront'],
        'conditionnel_present' => ['saurais', 'saurais', 'saurait', 'saurions', 'sauriez', 'sauraient'],
        'subjonctif_present' => ['sache', 'saches', 'sache', 'sachions', 'sachiez', 'sachent'],
        'imperatif_present' => ['sache', 'sachons', 'sachez'],
        'participe_present' => 'sachant',
        'participe_passe' => 'su'
    ],
    
    'pouvoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['peux', 'peux', 'peut', 'pouvons', 'pouvez', 'peuvent'],
        'imparfait' => ['pouvais', 'pouvais', 'pouvait', 'pouvions', 'pouviez', 'pouvaient'],
        'passe_simple' => ['pus', 'pus', 'put', 'pûmes', 'pûtes', 'purent'],
        'futur_simple' => ['pourrai', 'pourras', 'pourra', 'pourrons', 'pourrez', 'pourront'],
        'conditionnel_present' => ['pourrais', 'pourrais', 'pourrait', 'pourrions', 'pourriez', 'pourraient'],
        'subjonctif_present' => ['puisse', 'puisses', 'puisse', 'puissions', 'puissiez', 'puissent'],
        'imperatif_present' => null,
        'participe_present' => 'pouvant',
        'participe_passe' => 'pu'
    ],
    
    'vouloir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['veux', 'veux', 'veut', 'voulons', 'voulez', 'veulent'],
        'imparfait' => ['voulais', 'voulais', 'voulait', 'voulions', 'vouliez', 'voulaient'],
        'passe_simple' => ['voulus', 'voulus', 'voulut', 'voulûmes', 'voulûtes', 'voulurent'],
        'futur_simple' => ['voudrai', 'voudras', 'voudra', 'voudrons', 'voudrez', 'voudront'],
        'conditionnel_present' => ['voudrais', 'voudrais', 'voudrait', 'voudrions', 'voudriez', 'voudraient'],
        'subjonctif_present' => ['veuille', 'veuilles', 'veuille', 'voulions', 'vouliez', 'veuillent'],
        'imperatif_present' => ['veuille', 'voulons', 'veuillez'],
        'participe_present' => 'voulant',
        'participe_passe' => 'voulu'
    ],
    
    'devoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['dois', 'dois', 'doit', 'devons', 'devez', 'doivent'],
        'imparfait' => ['devais', 'devais', 'devait', 'devions', 'deviez', 'devaient'],
        'passe_simple' => ['dus', 'dus', 'dut', 'dûmes', 'dûtes', 'durent'],
        'futur_simple' => ['devrai', 'devras', 'devra', 'devrons', 'devrez', 'devront'],
        'conditionnel_present' => ['devrais', 'devrais', 'devrait', 'devrions', 'devriez', 'devraient'],
        'subjonctif_present' => ['doive', 'doives', 'doive', 'devions', 'deviez', 'doivent'],
        'imperatif_present' => ['dois', 'devons', 'devez'],
        'participe_present' => 'devant',
        'participe_passe' => 'dû'
    ],
    
    'recevoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['reçois', 'reçois', 'reçoit', 'recevons', 'recevez', 'reçoivent'],
        'imparfait' => ['recevais', 'recevais', 'recevait', 'recevions', 'receviez', 'recevaient'],
        'passe_simple' => ['reçus', 'reçus', 'reçut', 'reçûmes', 'reçûtes', 'reçurent'],
        'futur_simple' => ['recevrai', 'recevras', 'recevra', 'recevrons', 'recevrez', 'recevront'],
        'conditionnel_present' => ['recevrais', 'recevrais', 'recevrait', 'recevrions', 'recevriez', 'recevraient'],
        'subjonctif_present' => ['reçoive', 'reçoives', 'reçoive', 'recevions', 'receviez', 'reçoivent'],
        'imperatif_present' => ['reçois', 'recevons', 'recevez'],
        'participe_present' => 'recevant',
        'participe_passe' => 'reçu'
    ],
    
    'valoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['vaux', 'vaux', 'vaut', 'valons', 'valez', 'valent'],
        'imparfait' => ['valais', 'valais', 'valait', 'valions', 'valiez', 'valaient'],
        'passe_simple' => ['valus', 'valus', 'valut', 'valûmes', 'valûtes', 'valurent'],
        'futur_simple' => ['vaudrai', 'vaudras', 'vaudra', 'vaudrons', 'vaudrez', 'vaudront'],
        'conditionnel_present' => ['vaudrais', 'vaudrais', 'vaudrait', 'vaudrions', 'vaudriez', 'vaudraient'],
        'subjonctif_present' => ['vaille', 'vailles', 'vaille', 'valions', 'valiez', 'vaillent'],
        'imperatif_present' => ['vaux', 'valons', 'valez'],
        'participe_present' => 'valant',
        'participe_passe' => 'valu'
    ],
    
    'falloir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'impersonnel' => true,
        'present' => [null, null, 'faut', null, null, null],
        'imparfait' => [null, null, 'fallait', null, null, null],
        'passe_simple' => [null, null, 'fallut', null, null, null],
        'futur_simple' => [null, null, 'faudra', null, null, null],
        'conditionnel_present' => [null, null, 'faudrait', null, null, null],
        'subjonctif_present' => [null, null, 'faille', null, null, null],
        'imperatif_present' => null,
        'participe_present' => null,
        'participe_passe' => 'fallu'
    ],
    
    'pleuvoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'impersonnel' => true,
        'present' => [null, null, 'pleut', null, null, null],
        'imparfait' => [null, null, 'pleuvait', null, null, null],
        'passe_simple' => [null, null, 'plut', null, null, null],
        'futur_simple' => [null, null, 'pleuvra', null, null, null],
        'conditionnel_present' => [null, null, 'pleuvrait', null, null, null],
        'subjonctif_present' => [null, null, 'pleuve', null, null, null],
        'imperatif_present' => null,
        'participe_present' => 'pleuvant',
        'participe_passe' => 'plu'
    ],
    
    'asseoir' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['assieds', 'assieds', 'assied', 'asseyons', 'asseyez', 'asseyent'],
        'imparfait' => ['asseyais', 'asseyais', 'asseyait', 'asseyions', 'asseyiez', 'asseyaient'],
        'passe_simple' => ['assis', 'assis', 'assit', 'assîmes', 'assîtes', 'assirent'],
        'futur_simple' => ['assiérai', 'assiéras', 'assiéra', 'assiérons', 'assiérez', 'assiéront'],
        'conditionnel_present' => ['assiérais', 'assiérais', 'assiérait', 'assiérions', 'assiériez', 'assiéraient'],
        'subjonctif_present' => ['asseye', 'asseyes', 'asseye', 'asseyions', 'asseyiez', 'asseyent'],
        'imperatif_present' => ['assieds', 'asseyons', 'asseyez'],
        'participe_present' => 'asseyant',
        'participe_passe' => 'assis'
    ],

    // ==========================================
    // VERBES EN -RE
    // ==========================================
    
    'faire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['fais', 'fais', 'fait', 'faisons', 'faites', 'font'],
        'imparfait' => ['faisais', 'faisais', 'faisait', 'faisions', 'faisiez', 'faisaient'],
        'passe_simple' => ['fis', 'fis', 'fit', 'fîmes', 'fîtes', 'firent'],
        'futur_simple' => ['ferai', 'feras', 'fera', 'ferons', 'ferez', 'feront'],
        'conditionnel_present' => ['ferais', 'ferais', 'ferait', 'ferions', 'feriez', 'feraient'],
        'subjonctif_present' => ['fasse', 'fasses', 'fasse', 'fassions', 'fassiez', 'fassent'],
        'imperatif_present' => ['fais', 'faisons', 'faites'],
        'participe_present' => 'faisant',
        'participe_passe' => 'fait'
    ],
    
    'dire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['dis', 'dis', 'dit', 'disons', 'dites', 'disent'],
        'imparfait' => ['disais', 'disais', 'disait', 'disions', 'disiez', 'disaient'],
        'passe_simple' => ['dis', 'dis', 'dit', 'dîmes', 'dîtes', 'dirent'],
        'futur_simple' => ['dirai', 'diras', 'dira', 'dirons', 'direz', 'diront'],
        'conditionnel_present' => ['dirais', 'dirais', 'dirait', 'dirions', 'diriez', 'diraient'],
        'subjonctif_present' => ['dise', 'dises', 'dise', 'disions', 'disiez', 'disent'],
        'imperatif_present' => ['dis', 'disons', 'dites'],
        'participe_present' => 'disant',
        'participe_passe' => 'dit'
    ],
    
    'lire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['lis', 'lis', 'lit', 'lisons', 'lisez', 'lisent'],
        'imparfait' => ['lisais', 'lisais', 'lisait', 'lisions', 'lisiez', 'lisaient'],
        'passe_simple' => ['lus', 'lus', 'lut', 'lûmes', 'lûtes', 'lurent'],
        'futur_simple' => ['lirai', 'liras', 'lira', 'lirons', 'lirez', 'liront'],
        'conditionnel_present' => ['lirais', 'lirais', 'lirait', 'lirions', 'liriez', 'liraient'],
        'subjonctif_present' => ['lise', 'lises', 'lise', 'lisions', 'lisiez', 'lisent'],
        'imperatif_present' => ['lis', 'lisons', 'lisez'],
        'participe_present' => 'lisant',
        'participe_passe' => 'lu'
    ],
    
    'écrire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['écris', 'écris', 'écrit', 'écrivons', 'écrivez', 'écrivent'],
        'imparfait' => ['écrivais', 'écrivais', 'écrivait', 'écrivions', 'écriviez', 'écrivaient'],
        'passe_simple' => ['écrivis', 'écrivis', 'écrivit', 'écrivîmes', 'écrivîtes', 'écrivirent'],
        'futur_simple' => ['écrirai', 'écriras', 'écrira', 'écrirons', 'écrirez', 'écriront'],
        'conditionnel_present' => ['écrirais', 'écrirais', 'écrirait', 'écririons', 'écririez', 'écriraient'],
        'subjonctif_present' => ['écrive', 'écrives', 'écrive', 'écrivions', 'écriviez', 'écrivent'],
        'imperatif_present' => ['écris', 'écrivons', 'écrivez'],
        'participe_present' => 'écrivant',
        'participe_passe' => 'écrit'
    ],
    
    'prendre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['prends', 'prends', 'prend', 'prenons', 'prenez', 'prennent'],
        'imparfait' => ['prenais', 'prenais', 'prenait', 'prenions', 'preniez', 'prenaient'],
        'passe_simple' => ['pris', 'pris', 'prit', 'prîmes', 'prîtes', 'prirent'],
        'futur_simple' => ['prendrai', 'prendras', 'prendra', 'prendrons', 'prendrez', 'prendront'],
        'conditionnel_present' => ['prendrais', 'prendrais', 'prendrait', 'prendrions', 'prendriez', 'prendraient'],
        'subjonctif_present' => ['prenne', 'prennes', 'prenne', 'prenions', 'preniez', 'prennent'],
        'imperatif_present' => ['prends', 'prenons', 'prenez'],
        'participe_present' => 'prenant',
        'participe_passe' => 'pris'
    ],
    
    'mettre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['mets', 'mets', 'met', 'mettons', 'mettez', 'mettent'],
        'imparfait' => ['mettais', 'mettais', 'mettait', 'mettions', 'mettiez', 'mettaient'],
        'passe_simple' => ['mis', 'mis', 'mit', 'mîmes', 'mîtes', 'mirent'],
        'futur_simple' => ['mettrai', 'mettras', 'mettra', 'mettrons', 'mettrez', 'mettront'],
        'conditionnel_present' => ['mettrais', 'mettrais', 'mettrait', 'mettrions', 'mettriez', 'mettraient'],
        'subjonctif_present' => ['mette', 'mettes', 'mette', 'mettions', 'mettiez', 'mettent'],
        'imperatif_present' => ['mets', 'mettons', 'mettez'],
        'participe_present' => 'mettant',
        'participe_passe' => 'mis'
    ],
    
    'battre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['bats', 'bats', 'bat', 'battons', 'battez', 'battent'],
        'imparfait' => ['battais', 'battais', 'battait', 'battions', 'battiez', 'battaient'],
        'passe_simple' => ['battis', 'battis', 'battit', 'battîmes', 'battîtes', 'battirent'],
        'futur_simple' => ['battrai', 'battras', 'battra', 'battrons', 'battrez', 'battront'],
        'conditionnel_present' => ['battrais', 'battrais', 'battrait', 'battrions', 'battriez', 'battraient'],
        'subjonctif_present' => ['batte', 'battes', 'batte', 'battions', 'battiez', 'battent'],
        'imperatif_present' => ['bats', 'battons', 'battez'],
        'participe_present' => 'battant',
        'participe_passe' => 'battu'
    ],
    
    'connaître' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['connais', 'connais', 'connaît', 'connaissons', 'connaissez', 'connaissent'],
        'imparfait' => ['connaissais', 'connaissais', 'connaissait', 'connaissions', 'connaissiez', 'connaissaient'],
        'passe_simple' => ['connus', 'connus', 'connut', 'connûmes', 'connûtes', 'connurent'],
        'futur_simple' => ['connaîtrai', 'connaîtras', 'connaîtra', 'connaîtrons', 'connaîtrez', 'connaîtront'],
        'conditionnel_present' => ['connaîtrais', 'connaîtrais', 'connaîtrait', 'connaîtrions', 'connaîtriez', 'connaîtraient'],
        'subjonctif_present' => ['connaisse', 'connaisses', 'connaisse', 'connaissions', 'connaissiez', 'connaissent'],
        'imperatif_present' => ['connais', 'connaissons', 'connaissez'],
        'participe_present' => 'connaissant',
        'participe_passe' => 'connu'
    ],
    
    'naître' => [
        'groupe' => 3,
        'auxiliaire' => 'être',
        'present' => ['nais', 'nais', 'naît', 'naissons', 'naissez', 'naissent'],
        'imparfait' => ['naissais', 'naissais', 'naissait', 'naissions', 'naissiez', 'naissaient'],
        'passe_simple' => ['naquis', 'naquis', 'naquit', 'naquîmes', 'naquîtes', 'naquirent'],
        'futur_simple' => ['naîtrai', 'naîtras', 'naîtra', 'naîtrons', 'naîtrez', 'naîtront'],
        'conditionnel_present' => ['naîtrais', 'naîtrais', 'naîtrait', 'naîtrions', 'naîtriez', 'naîtraient'],
        'subjonctif_present' => ['naisse', 'naisses', 'naisse', 'naissions', 'naissiez', 'naissent'],
        'imperatif_present' => ['nais', 'naissons', 'naissez'],
        'participe_present' => 'naissant',
        'participe_passe' => 'né'
    ],
    
    'croire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['crois', 'crois', 'croit', 'croyons', 'croyez', 'croient'],
        'imparfait' => ['croyais', 'croyais', 'croyait', 'croyions', 'croyiez', 'croyaient'],
        'passe_simple' => ['crus', 'crus', 'crut', 'crûmes', 'crûtes', 'crurent'],
        'futur_simple' => ['croirai', 'croiras', 'croira', 'croirons', 'croirez', 'croiront'],
        'conditionnel_present' => ['croirais', 'croirais', 'croirait', 'croirions', 'croiriez', 'croiraient'],
        'subjonctif_present' => ['croie', 'croies', 'croie', 'croyions', 'croyiez', 'croient'],
        'imperatif_present' => ['crois', 'croyons', 'croyez'],
        'participe_present' => 'croyant',
        'participe_passe' => 'cru'
    ],
    
    'boire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['bois', 'bois', 'boit', 'buvons', 'buvez', 'boivent'],
        'imparfait' => ['buvais', 'buvais', 'buvait', 'buvions', 'buviez', 'buvaient'],
        'passe_simple' => ['bus', 'bus', 'but', 'bûmes', 'bûtes', 'burent'],
        'futur_simple' => ['boirai', 'boiras', 'boira', 'boirons', 'boirez', 'boiront'],
        'conditionnel_present' => ['boirais', 'boirais', 'boirait', 'boirions', 'boiriez', 'boiraient'],
        'subjonctif_present' => ['boive', 'boives', 'boive', 'buvions', 'buviez', 'boivent'],
        'imperatif_present' => ['bois', 'buvons', 'buvez'],
        'participe_present' => 'buvant',
        'participe_passe' => 'bu'
    ],
    
    'plaire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['plais', 'plais', 'plaît', 'plaisons', 'plaisez', 'plaisent'],
        'imparfait' => ['plaisais', 'plaisais', 'plaisait', 'plaisions', 'plaisiez', 'plaisaient'],
        'passe_simple' => ['plus', 'plus', 'plut', 'plûmes', 'plûtes', 'plurent'],
        'futur_simple' => ['plairai', 'plairas', 'plaira', 'plairons', 'plairez', 'plairont'],
        'conditionnel_present' => ['plairais', 'plairais', 'plairait', 'plairions', 'plairiez', 'plairaient'],
        'subjonctif_present' => ['plaise', 'plaises', 'plaise', 'plaisions', 'plaisiez', 'plaisent'],
        'imperatif_present' => ['plais', 'plaisons', 'plaisez'],
        'participe_present' => 'plaisant',
        'participe_passe' => 'plu'
    ],
    
    'vivre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['vis', 'vis', 'vit', 'vivons', 'vivez', 'vivent'],
        'imparfait' => ['vivais', 'vivais', 'vivait', 'vivions', 'viviez', 'vivaient'],
        'passe_simple' => ['vécus', 'vécus', 'vécut', 'vécûmes', 'vécûtes', 'vécurent'],
        'futur_simple' => ['vivrai', 'vivras', 'vivra', 'vivrons', 'vivrez', 'vivront'],
        'conditionnel_present' => ['vivrais', 'vivrais', 'vivrait', 'vivrions', 'vivriez', 'vivraient'],
        'subjonctif_present' => ['vive', 'vives', 'vive', 'vivions', 'viviez', 'vivent'],
        'imperatif_present' => ['vis', 'vivons', 'vivez'],
        'participe_present' => 'vivant',
        'participe_passe' => 'vécu'
    ],
    
    'suivre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['suis', 'suis', 'suit', 'suivons', 'suivez', 'suivent'],
        'imparfait' => ['suivais', 'suivais', 'suivait', 'suivions', 'suiviez', 'suivaient'],
        'passe_simple' => ['suivis', 'suivis', 'suivit', 'suivîmes', 'suivîtes', 'suivirent'],
        'futur_simple' => ['suivrai', 'suivras', 'suivra', 'suivrons', 'suivrez', 'suivront'],
        'conditionnel_present' => ['suivrais', 'suivrais', 'suivrait', 'suivrions', 'suivriez', 'suivraient'],
        'subjonctif_present' => ['suive', 'suives', 'suive', 'suivions', 'suiviez', 'suivent'],
        'imperatif_present' => ['suis', 'suivons', 'suivez'],
        'participe_present' => 'suivant',
        'participe_passe' => 'suivi'
    ],
    
    'rire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['ris', 'ris', 'rit', 'rions', 'riez', 'rient'],
        'imparfait' => ['riais', 'riais', 'riait', 'riions', 'riiez', 'riaient'],
        'passe_simple' => ['ris', 'ris', 'rit', 'rîmes', 'rîtes', 'rirent'],
        'futur_simple' => ['rirai', 'riras', 'rira', 'rirons', 'rirez', 'riront'],
        'conditionnel_present' => ['rirais', 'rirais', 'rirait', 'ririons', 'ririez', 'riraient'],
        'subjonctif_present' => ['rie', 'ries', 'rie', 'riions', 'riiez', 'rient'],
        'imperatif_present' => ['ris', 'rions', 'riez'],
        'participe_present' => 'riant',
        'participe_passe' => 'ri'
    ],
    
    'conduire' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['conduis', 'conduis', 'conduit', 'conduisons', 'conduisez', 'conduisent'],
        'imparfait' => ['conduisais', 'conduisais', 'conduisait', 'conduisions', 'conduisiez', 'conduisaient'],
        'passe_simple' => ['conduisis', 'conduisis', 'conduisit', 'conduisîmes', 'conduisîtes', 'conduisirent'],
        'futur_simple' => ['conduirai', 'conduiras', 'conduira', 'conduirons', 'conduirez', 'conduiront'],
        'conditionnel_present' => ['conduirais', 'conduirais', 'conduirait', 'conduirions', 'conduiriez', 'conduiraient'],
        'subjonctif_present' => ['conduise', 'conduises', 'conduise', 'conduisions', 'conduisiez', 'conduisent'],
        'imperatif_present' => ['conduis', 'conduisons', 'conduisez'],
        'participe_present' => 'conduisant',
        'participe_passe' => 'conduit'
    ],
    
    'craindre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['crains', 'crains', 'craint', 'craignons', 'craignez', 'craignent'],
        'imparfait' => ['craignais', 'craignais', 'craignait', 'craignions', 'craigniez', 'craignaient'],
        'passe_simple' => ['craignis', 'craignis', 'craignit', 'craignîmes', 'craignîtes', 'craignirent'],
        'futur_simple' => ['craindrai', 'craindras', 'craindra', 'craindrons', 'craindrez', 'craindront'],
        'conditionnel_present' => ['craindrais', 'craindrais', 'craindrait', 'craindrions', 'craindriez', 'craindraient'],
        'subjonctif_present' => ['craigne', 'craignes', 'craigne', 'craignions', 'craigniez', 'craignent'],
        'imperatif_present' => ['crains', 'craignons', 'craignez'],
        'participe_present' => 'craignant',
        'participe_passe' => 'craint'
    ],
    
    'peindre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['peins', 'peins', 'peint', 'peignons', 'peignez', 'peignent'],
        'imparfait' => ['peignais', 'peignais', 'peignait', 'peignions', 'peigniez', 'peignaient'],
        'passe_simple' => ['peignis', 'peignis', 'peignit', 'peignîmes', 'peignîtes', 'peignirent'],
        'futur_simple' => ['peindrai', 'peindras', 'peindra', 'peindrons', 'peindrez', 'peindront'],
        'conditionnel_present' => ['peindrais', 'peindrais', 'peindrait', 'peindrions', 'peindriez', 'peindraient'],
        'subjonctif_present' => ['peigne', 'peignes', 'peigne', 'peignions', 'peigniez', 'peignent'],
        'imperatif_present' => ['peins', 'peignons', 'peignez'],
        'participe_present' => 'peignant',
        'participe_passe' => 'peint'
    ],
    
    'joindre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['joins', 'joins', 'joint', 'joignons', 'joignez', 'joignent'],
        'imparfait' => ['joignais', 'joignais', 'joignait', 'joignions', 'joigniez', 'joignaient'],
        'passe_simple' => ['joignis', 'joignis', 'joignit', 'joignîmes', 'joignîtes', 'joignirent'],
        'futur_simple' => ['joindrai', 'joindras', 'joindra', 'joindrons', 'joindrez', 'joindront'],
        'conditionnel_present' => ['joindrais', 'joindrais', 'joindrait', 'joindrions', 'joindriez', 'joindraient'],
        'subjonctif_present' => ['joigne', 'joignes', 'joigne', 'joignions', 'joigniez', 'joignent'],
        'imperatif_present' => ['joins', 'joignons', 'joignez'],
        'participe_present' => 'joignant',
        'participe_passe' => 'joint'
    ],
    
    'vaincre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['vaincs', 'vaincs', 'vainc', 'vainquons', 'vainquez', 'vainquent'],
        'imparfait' => ['vainquais', 'vainquais', 'vainquait', 'vainquions', 'vainquiez', 'vainquaient'],
        'passe_simple' => ['vainquis', 'vainquis', 'vainquit', 'vainquîmes', 'vainquîtes', 'vainquirent'],
        'futur_simple' => ['vaincrai', 'vaincras', 'vaincra', 'vaincrons', 'vaincrez', 'vaincront'],
        'conditionnel_present' => ['vaincrais', 'vaincrais', 'vaincrait', 'vaincrions', 'vaincriez', 'vaincraient'],
        'subjonctif_present' => ['vainque', 'vainques', 'vainque', 'vainquions', 'vainquiez', 'vainquent'],
        'imperatif_present' => ['vaincs', 'vainquons', 'vainquez'],
        'participe_present' => 'vainquant',
        'participe_passe' => 'vaincu'
    ],
    
    'résoudre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['résous', 'résous', 'résout', 'résolvons', 'résolvez', 'résolvent'],
        'imparfait' => ['résolvais', 'résolvais', 'résolvait', 'résolvions', 'résolviez', 'résolvaient'],
        'passe_simple' => ['résolus', 'résolus', 'résolut', 'résolûmes', 'résolûtes', 'résolurent'],
        'futur_simple' => ['résoudrai', 'résoudras', 'résoudra', 'résoudrons', 'résoudrez', 'résoudront'],
        'conditionnel_present' => ['résoudrais', 'résoudrais', 'résoudrait', 'résoudrions', 'résoudriez', 'résoudraient'],
        'subjonctif_present' => ['résolve', 'résolves', 'résolve', 'résolvions', 'résolviez', 'résolvent'],
        'imperatif_present' => ['résous', 'résolvons', 'résolvez'],
        'participe_present' => 'résolvant',
        'participe_passe' => 'résolu'
    ],
    
    'coudre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['couds', 'couds', 'coud', 'cousons', 'cousez', 'cousent'],
        'imparfait' => ['cousais', 'cousais', 'cousait', 'cousions', 'cousiez', 'cousaient'],
        'passe_simple' => ['cousis', 'cousis', 'cousit', 'cousîmes', 'cousîtes', 'cousirent'],
        'futur_simple' => ['coudrai', 'coudras', 'coudra', 'coudrons', 'coudrez', 'coudront'],
        'conditionnel_present' => ['coudrais', 'coudrais', 'coudrait', 'coudrions', 'coudriez', 'coudraient'],
        'subjonctif_present' => ['couse', 'couses', 'couse', 'cousions', 'cousiez', 'cousent'],
        'imperatif_present' => ['couds', 'cousons', 'cousez'],
        'participe_present' => 'cousant',
        'participe_passe' => 'cousu'
    ],
    
    'moudre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['mouds', 'mouds', 'moud', 'moulons', 'moulez', 'moulent'],
        'imparfait' => ['moulais', 'moulais', 'moulait', 'moulions', 'mouliez', 'moulaient'],
        'passe_simple' => ['moulus', 'moulus', 'moulut', 'moulûmes', 'moulûtes', 'moulurent'],
        'futur_simple' => ['moudrai', 'moudras', 'moudra', 'moudrons', 'moudrez', 'moudront'],
        'conditionnel_present' => ['moudrais', 'moudrais', 'moudrait', 'moudrions', 'moudriez', 'moudraient'],
        'subjonctif_present' => ['moule', 'moules', 'moule', 'moulions', 'mouliez', 'moulent'],
        'imperatif_present' => ['mouds', 'moulons', 'moulez'],
        'participe_present' => 'moulant',
        'participe_passe' => 'moulu'
    ],
    
    'conclure' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['conclus', 'conclus', 'conclut', 'concluons', 'concluez', 'concluent'],
        'imparfait' => ['concluais', 'concluais', 'concluait', 'concluions', 'concluiez', 'concluaient'],
        'passe_simple' => ['conclus', 'conclus', 'conclut', 'conclûmes', 'conclûtes', 'conclurent'],
        'futur_simple' => ['conclurai', 'concluras', 'conclura', 'conclurons', 'conclurez', 'concluront'],
        'conditionnel_present' => ['conclurais', 'conclurais', 'conclurait', 'conclurions', 'concluriez', 'concluraient'],
        'subjonctif_present' => ['conclue', 'conclues', 'conclue', 'concluions', 'concluiez', 'concluent'],
        'imperatif_present' => ['conclus', 'concluons', 'concluez'],
        'participe_present' => 'concluant',
        'participe_passe' => 'conclu'
    ],
    
    'rompre' => [
        'groupe' => 3,
        'auxiliaire' => 'avoir',
        'present' => ['romps', 'romps', 'rompt', 'rompons', 'rompez', 'rompent'],
        'imparfait' => ['rompais', 'rompais', 'rompait', 'rompions', 'rompiez', 'rompaient'],
        'passe_simple' => ['rompis', 'rompis', 'rompit', 'rompîmes', 'rompîtes', 'rompirent'],
        'futur_simple' => ['romprai', 'rompras', 'rompra', 'romprons', 'romprez', 'rompront'],
        'conditionnel_present' => ['romprais', 'romprais', 'romprait', 'romprions', 'rompriez', 'rompraient'],
        'subjonctif_present' => ['rompe', 'rompes', 'rompe', 'rompions', 'rompiez', 'rompent'],
        'imperatif_present' => ['romps', 'rompons', 'rompez'],
        'participe_present' => 'rompant',
        'participe_passe' => 'rompu'
    ]
];
