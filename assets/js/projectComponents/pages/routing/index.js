import api from '../api/index'

const loadPageBeforeEnter = async (to, from, next) => {
  try {
    to.params.page = await api.get(to.params.page)
    next()
  } catch (e) {
    console.error(e)
  }
}

const loadBook = async (to, from, next) => {
  try {
    to.params.book = await api.getBook(to.params.bookId);
    next();
  } catch (e) {
    console.error(e);
  }
}

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
      to.params.books = books;
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
    path: '/edit/:page',
    name: 'edit',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/edit.vue'),
    props: true,
    beforeEnter: loadPageBeforeEnter
  },
  {
    path: '/:page',
    name: 'show',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/show.vue'),
    props: true,
    beforeEnter: loadPageBeforeEnter
  },
  {
    path: '/books/:bookId',
    name: 'book',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/book.vue'),
    props: true,
    beforeEnter: loadBook,
    children: [
      {
        path: '/:page',
        name: 'book_page',
        component: () => import(/* webpackChunkName: "pages" */ '../pages/show.vue'),
        props: true,
        beforeEnter: loadPageBeforeEnter
      },
    ]
  }
]
