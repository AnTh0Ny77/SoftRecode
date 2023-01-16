class TokenManager {
    constructor(baseURL) {
      this.baseURL = baseURL;
      this.tokenKey = 'access_token';
      this.refreshTokenKey = 'refresh_token';
    }
  
    // Récupère le token d'accès stocké dans le navigateur
    get token() {
      return localStorage.getItem(this.tokenKey);
    }
  
    // Stocke le token d'accès dans le navigateur
    set token(value) {
      localStorage.setItem(this.tokenKey, value);
    }
  
    // Récupère le refresh token stocké dans le navigateur
    get refreshToken() {
      return localStorage.getItem(this.refreshTokenKey);
    }
  
    // Stocke le refresh token dans le navigateur
    set refreshToken(value) {
      localStorage.setItem(this.refreshTokenKey, value);
    }
  
    async login() {
      // Requête de login pour obtenir un token et un refresh_token
      const response = await fetch(`${this.baseURL}/login`, {
        method: 'POST',
        body: JSON.stringify({username: 'myusername', password: 'mypassword'}),
        headers: { 'Content-Type': 'application/json' },
      });
      const data = await response.json();
      this.token = data.token;
      this.refreshToken = data.refresh_token;
    }
  
    async refreshToken() {
      // Requête pour rafraîchir le token
      const response = await fetch(`${this.baseURL}/refresh_token`, {
        method: 'POST',
        body: JSON.stringify({refresh_token: this.refreshToken}),
        headers: { 'Content-Type': 'application/json' },
      });
      const data = await response.json();
      this.token = data.token;
    }
  
    async makeRequest(url, options = {}) {
      // Vérifie si un token existe, sinon effectue une demande de login
      if (!this.token) {
        await this.login();
      }
  
      // Ajoute le token d'accès dans les en-têtes de la requête
      options.headers = options.headers || {};
      options.headers.Authorization = `Bearer ${this.token}`;
  
      // Effectue la requête
      const response = await fetch(`${this.baseURL}${url}`, options);
  
      // Vérifie si un code 498 est reçu et effectue une demande de rafraîchissement de token
      if (response.status === 498) {
        await this.refreshToken();
        // Réessaye la requête avec le nouveau token
        options.headers.Authorization = `Bearer ${this.token}`;

        // Vérifie si un code 401 est reçu et effectue une demande de login
        if (response.status === 401) {
          await this.login();
          // Réessaye la requête avec le nouveau token
          options.headers.Authorization = `Bearer ${this.token}`;
          return await fetch(`${this.baseURL}${url}`, options);
        }
        return response;
      }
    }
}