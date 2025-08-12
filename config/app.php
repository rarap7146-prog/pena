<?php
return [
  'base_url'    => 'https://araska.id',
  'uploads_dir' => __DIR__ . '/../storage/uploads',
  'db' => [
    'dsn'  => 'mysql:host=127.0.0.1;dbname=araska;charset=utf8mb4',
    'user' => 'araska_user',
    'pass' => 'SandiKuat93',
  ],
  // NEW:
  'auth' => [
    // replace with the hash you generated:
    'admin_password_hash' => '$2y$10$Vtz.JN.K0qZQ/eSX/MoXO.bCfRhr5gizEQrCyGTOd.MDY5LLCT5dm'
  ],
];
