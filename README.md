![startCodeigneiter CSM](https://repository-images.githubusercontent.com/233129678/7ad83200-f12e-11ea-8538-ab49ede15585)

# startCodeigneiter CSM

startCodeIgniter CSM is a Lightweight Content Managemant System based on Codeigneiter Framework and Vuejs: cloud-enabled, mobile-ready, offline-storage and HTML5 editor.

- Create custom pages, blogs
- Manage your files and folders
- Manage events, videos and photos
- Create User with levels acces
- Create categories/ subcategories

# New Features!

- Dinamic Forms contents
- New dashboard witgeds addeds
  You can also:
- Use the API to consume the data created in the interface
- Import the postman collection with some examples of use
- Change de configuration and theme of yor website

### Tech

startCodeIgniter CSM uses a number of open source projects to work properly:

- [VueJS] - HTML enhanced for web apps!
- [tinymce] - awesome web-based text editor
- [Materialize] - great UI boilerplate for modern web apps
- [MySQL] - the popular database for the storage
- [Codeigneiter] - fast PHP app framework
- [Gulp] - the streaming build system
- [jQuery] - duh

And of course startCodeIgniter CSM itself is open source with a [public repository][startcodeigniter]
on GitHub.

### Installation

startCodeIgniter CSM requires [Composer](https://getcomposer.org/).

Install the dependencies and devDependencies and start the server.

```sh
$ git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
$ cd ./startCodeIgniter-CSM
$ composer install
$ npm install
$ php -S localhost:8000 -t ./
```

• startCodeIgniter CSM private panel admin will be in [/admin](https://localhost:8000/admin/).
• startCodeIgniter CSM public website will be in [/](https://localhost:8000/).

### Development

Want to contribute? Great!

startCodeIgniter CSM uses Gulp for fast developing.
Make a change in your file and instantaneously see your updates!

Open your favorite Terminal and run these commands.

First Tab:

```sh
$ php -S localhost:8000 -t ./
```

Second Tab:

```sh
$ gulp watch_resources
```

### Todos

- Write MORE Tests
- Add Night Mode

## License

MIT

**Free Software, Hell Yeah!**

[//]: # "These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax"
[startcodeigniter]: https://github.com/gervisbermudez/startCodeIgniter-CSM
[git-repo-url]: https://github.com/gervisbermudez/startCodeIgniter-CSM.git
[df1]: http://daringfireball.net/projects/markdown/
[codeigneiter]: https://github.com/bcit-ci/CodeIgniter
[node.js]: http://nodejs.org
[twitter bootstrap]: http://twitter.github.com/bootstrap/
[jquery]: http://jquery.com
[@tjholowaychuk]: http://twitter.com/tjholowaychuk
[vuejs]: https://github.com/vuejs/vue
[gulp]: http://gulpjs.com
[materialize]: https://github.com/Dogfalo/materialize
