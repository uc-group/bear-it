# Project components

To add project component create a folder in the `assets/js/projectComponents`.
The name of the folder will be the name of the component and will be part of the url.
The naming convention is with dashes `my-component`.

In component folder there must be a `config.js` file:

```js
export default {
  name: 'Test component',  // name of the component
  description: 'Description of test component', // description
  icon: 'mdi-package-variant', // icon, optional if not present will use 'mdi-package-variant
  menu: [ // optional, will add this links to the menu project in the left toolbar
    { label: 'Test link', target: '/', icon: null },
    { label: 'Test link2', target: '/', icon: 'mdi-home' }
  ]
}
```

## Menu

Menu is optional. Label and icon will be displayed on the left toolbar in the project menu.
The target can be a string, or the same object used in vue-router. It also can be a function.
If it is a function then it will get the project module store state as argument. It should return
a valid vue-router link.

```js
  {
    label: 'Project members', 
    link: (project) => ({ 
      name: 'project_details', 
      params: { id: project.id, tab: 'members' } 
    }) 
  }
```
