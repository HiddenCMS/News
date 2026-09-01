# HiddenCMS News

Module d'actualites officiel pour HiddenCMS. Le paquet contient le module
`news`, son widget, la migration de son schema SQL et ses donnees initiales.

```bash
composer require hiddencms/news
```

L'activation du module et du widget reste explicite depuis l'administration
HiddenCMS. La suppression Composer conserve les actualites par defaut ; la
purge des donnees doit etre demandee dans la modale de suppression.
