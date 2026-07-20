# Cheatsheet CodeIgniter 4 — Filters, Validation, Layouts, Query Builder

> Complément au noyau MVC de base (Route → Contrôleur → Modèle → Vue).
> À consulter quand tu as besoin d'une de ces briques, pas à apprendre par cœur d'un coup.

---

## 1. Filters (middlewares) — protéger des routes

**Rôle** : intercepter une requête avant (ou après) le contrôleur, pour bloquer l'accès si une condition n'est pas remplie (connexion, rôle...).

### Créer un filtre
`app/Filters/AuthFilter.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('user')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // exécuté après le contrôleur, souvent vide
    }
}
```
- `before()` : si elle `return` une Response (redirect), CI arrête tout et n'appelle jamais le contrôleur.
- `$arguments` : reçoit les paramètres passés après `:` dans la config des routes (voir plus bas).

### Déclarer le filtre
`app/Config/Filters.php`
```php
public array $aliases = [
    'csrf' => \CodeIgniter\Filters\CSRF::class,
    'auth' => AuthFilter::class,
    'role' => RoleFilter::class,
];

public array $globals = [
    'before' => ['csrf'], // appliqué à TOUTES les routes
];
```

### Appliquer sur des routes
```php
// Une seule route
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Un groupe de routes
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/livres/ajouter', 'LivreController::form');
});

// Avec paramètre (role:admin)
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
});
```

### Comment `'role:admin'` devient `$arguments`
```
'role:admin,bibliothecaire'
   │              │
 nom filtre    paramètres séparés par ','
   │              │
   ▼              ▼
$aliases['role']   →   $arguments = ['admin', 'bibliothecaire']
```
CI découpe la chaîne (`explode(':')` puis `explode(',')`), retrouve la classe via `$aliases`, puis appelle `before($request, $arguments)`.

---

## 2. Validation — vérifier les données avant insertion

**Rôle** : empêcher d'enregistrer des données invalides (champ vide, email mal formé, etc.) directement au niveau du Model.

### Dans le Model
```php
class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['name', 'email', 'password'];

    protected $validationRules = [
        'name'             => 'required|min_length[3]',
        'email'            => 'required|valid_email',
        'password'         => 'required|min_length[6]',
        'confirm_password' => 'required|matches[password]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Le nom est obligatoire',
            'min_length' => 'Minimum 3 caractères',
        ],
    ];
}
```
- Le `|` sépare plusieurs règles pour un même champ.
- `confirm_password` peut être validé sans être inséré (il suffit qu'il soit absent de `$allowedFields`).

### Règles courantes
| Règle | Effet |
|---|---|
| `required` | champ obligatoire |
| `min_length[3]` / `max_length[50]` | longueur |
| `valid_email` | format email |
| `matches[password]` | doit être identique à un autre champ |
| `is_unique[users.email]` | doit être unique en base |
| `greater_than[17]` | comparaison numérique |
| `numeric` / `alpha` / `alpha_numeric` | type de contenu |

### Dans le contrôleur
```php
public function store()
{
    $model = new UserModel();
    $data = $this->request->getPost();

    if (!$model->insert($data)) {
        return view('form', ['validation' => $model->errors()]);
    }
    return redirect()->to('/users');
}
```
- `insert()` retourne `false` si la validation échoue (rien n'est inséré).
- `$model->errors()` → tableau `['champ' => 'message']`.

### Dans la vue
```php
<input type="text" name="name">
<div><?= $validation['name'] ?? '' ?></div>
```

---

## 3. Layouts / Templating — éviter de répéter le HTML

**Rôle** : avoir un squelette commun (header, footer...) rempli différemment selon la page.

### Le squelette
`app/Views/layout.php`
```php
<!DOCTYPE html>
<html>
<head><title><?= $title ?></title></head>
<body>
    <?= $this->renderSection('content') ?>
</body>
</html>
```

### Une page qui utilise ce squelette
`app/Views/home.php`
```php
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
    <h1>Accueil</h1>
<?= $this->endSection() ?>
```

### Le contrôleur ne change pas
```php
return view('home', ['title' => 'Accueil']);
```

| Fonction | Rôle |
|---|---|
| `extend('layout')` | "j'utilise ce squelette" |
| `section('content')` / `endSection()` | définit le contenu qui remplit le trou |
| `renderSection('content')` | le "trou" dans le layout |

---

## 4. Query Builder — CRUD et agrégats

### CRUD basique (via Model)
```php
$model->insert(['name' => 'John', 'email' => 'john@mail.com']); // Create
$model->findAll();                                              // Read (tous)
$model->find(1);                                                 // Read (un seul)
$model->update(1, ['name' => 'John Updated']);                  // Update
$model->delete(1);                                               // Delete
```

### Filtrer une requête
```php
$user = $model->where('email', $email)->first(); // 1 résultat
$users = $model->where('role', 'admin')->findAll(); // plusieurs résultats
```

### Agrégats (via `$db` directement, pas un Model)
```php
$count = $db->table('users')->countAllResults();
$sum   = $db->table('orders')->selectSum('amount')->get()->getRow();
$avg   = $db->table('orders')->selectAvg('amount')->get()->getRow();
$max   = $db->table('orders')->selectMax('amount')->get()->getRow();
```
- `->get()` exécute la requête
- `->getRow()` → une seule ligne (agrégats)
- `->getResult()` / `->getResultArray()` → plusieurs lignes

### SQL direct (cas complexes)
```php
$query  = $db->query("SELECT * FROM users WHERE id = ?", [1]);
$result = $query->getResult();
```
Le `?` est un placeholder sécurisé contre l'injection SQL — toujours préférer ça à la concaténation de variables dans une requête.

---

## 5. CSRF — activer la protection des formulaires

```php
// app/Config/Filters.php
public array $globals = [
    'before' => ['csrf'],
];
```
```php
<!-- dans chaque formulaire -->
<form method="post" action="/articles">
    <?= csrf_field() ?>
    ...
</form>
```

---

## Bonnes pratiques à retenir

- Toujours valider les entrées utilisateur (`$validationRules` ou vérification manuelle).
- Toujours activer CSRF sur les formulaires.
- Toujours utiliser `esc()` en vue pour afficher des données utilisateur (anti-XSS).
- Préférer le Query Builder au SQL brut ; réserver le SQL direct aux cas complexes.
- Séparer Model / Contrôleur / Vue — ne pas mettre de logique SQL dans un contrôleur.
- Utiliser un layout dès que plusieurs pages partagent une structure commune.