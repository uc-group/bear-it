function urlEncodeB64(input) {
  const b64Chars = { '+': '-', '/': '_', '=': '' };
  return input.replace(/[+/=]/g, (m) => b64Chars[m]);
}

function bufferToBase64UrlEncoded(input) {
  const bytes = new Uint8Array(input);
  return urlEncodeB64(window.btoa(String.fromCharCode(...bytes)));
}


export default async (message) => {
  const encoder = new TextEncoder();
  const data = encoder.encode(message);

  return window.crypto.subtle.digest('SHA-256', data).then(bufferToBase64UrlEncoded);
}
