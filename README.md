# Projet Symfony avec Intégration de Template HTML Tailwind

## Routes

Le projet contient les routes suivantes :

- **/home** : Page d'accueil
- **/login** : Page de connexion
- **/register** : Page d'inscription
- **/shop** : Page de la boutique
- **/product** : Page de détail du produit
- **/not-found** : Page 404
- **/cart** : Page du panier
- **/checkout** : Page de paiement

## Tester les routes
**Démarrer le serveur Symfony** :
   ```bash
   symfony server:start 
   ```

## Modification du style
Pour modifier le style du projet, suivez les étapes ci-dessous :
1.Modifier le fichier `tailwind.config.js` situé dans le répertoire `/public/template-html` :
```yaml
colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: '#fdfcf3',
      black: '#3b302f',
      primary: '#d97706',
      secondary: '#6b705c',
      accent: '#cb997e',
      gray: {
        lighter: '#ede0d4',
        light: '#a5a58d',
        dark: 'rgba(107,112,92,0.83)',
        txt: '#4f4a41',
        line: '#d6ccc2',
      },
```
2.Exécuter les commandes suivantes pour compiler les modifications :
```bash 
npm run dev
```
3. Pour créer la version de production :
```bash
npm run build
```
