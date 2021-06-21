export default (length) => {
  const validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let array = new Uint8Array(length);
  window.crypto.getRandomValues(array);
  array = array.map((x) => validChars.charCodeAt(x % validChars.length));

  return String.fromCharCode(...array);
}
