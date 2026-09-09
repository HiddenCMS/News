# HiddenCMS News

Module d'actualites officiel pour HiddenCMS. Le paquet contient le module
`news`, son widget, la migration de son schema SQL et ses donnees initiales.

```bash
composer require hiddencms/news
```

L'activation du module et du widget reste explicite depuis l'administration
HiddenCMS. La suppression Composer conserve les actualites par defaut ; la
purge des donnees doit etre demandee dans la modale de suppression.

## Donnees personnelles

Le module participe a l'export RGPD du core en fournissant les actualites
redigees par l'utilisateur et leurs traductions. Lors de l'effacement d'un
compte, les actualites publiees sont conservees comme contributions editoriales
et attribuees au compte anonymise par le core, sans lien vers un profil.

Le contrat peut etre verifie en lecture seule sur une installation locale :

```bash
php tests/privacy-contract.php /chemin/vers/hiddencms
```
