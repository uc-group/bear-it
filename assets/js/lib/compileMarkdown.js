import marked from 'marked'
import DOMPurify from 'dompurify'

export default (html) => html ? DOMPurify.sanitize(marked(html)) : ""
