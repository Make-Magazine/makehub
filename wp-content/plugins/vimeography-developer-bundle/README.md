## Installing theme deps

`npm run bootstrap`

## Adding a new shared npm package (adds to each theme's package.json)

`lerna add react-dom --hoist`

## Adding a npm package/version specific to a theme

Change to the theme dir and add it to the package.json manually, then bootstrap via lerna root dir.

Package definitions _could_ just be added to the root `package.json` file and subthemes would pick them up.

However this would not specify the dependencies of each theme for clarity sake and would not allow themes to be their own portable packages.

So instead, we manage package versions with lerna which updates each theme's `package.json` file automatically.

## Working on themes

1.) Start the `vimeography-blueprint` watcher locally

```
cd wp-content
git clone git@github.com:davekiss/vimeography-blueprint.git
cd wp-content/vimeography-blueprint
npm install
npm run watch
```

2.) Serve the theme build from a local webpack server

```
cd wp-content/plugins/vimeography-developer-bundle/vimeography-themes/vimeography-aloha
npm run start
```

## Production build all themes

```
cd wp-content/plugins/vimeography-developer-bundle && npm run build
```
