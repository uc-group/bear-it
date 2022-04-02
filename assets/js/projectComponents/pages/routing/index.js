import api from '../api/index'

export default [
  {
    path: '/',
    name: 'index',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/index.vue'),
    beforeEnter: async (to, from, next) => {
      const [ pages, books ] = await Promise.all([
        api.list(to.params.project.id),
        api.listBooks(to.params.project.id)
      ]);
      to.params.books = books.map((book) => {
        const [project, number] = book.id.split('-');
        book.number = number;
        book.project = project;

        return book;
      });
      to.params.pages = pages;
      next()
    }
  },
  {
    path: '/create',
    name: 'create',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/create.vue')
  },
  {
    path: '/edit/:pageId',
    name: 'edit',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/edit.vue')
  },
  {
    path: '/:pageId',
    name: 'show',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/show.vue'),
  },
  {
    path: '/books/:bookNumber',
    name: 'book',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/book.vue'),
    children: [
      {
        path: 'page/:pageId',
        name: 'book_page',
        component: () => import(/* webpackChunkName: "pages" */ '../pages/show.vue'),
      },
      {
        path: 'edit/:pageId',
        name: 'book_edit_page',
        component: () => import(/* webpackChunkName: "pages" */ '../pages/edit.vue'),
      },
    ]
  }
]
