class Authentication {
    async isLoggedIn() {
        return new Promise((resolve, reject) => {
            axios.get('/ajax/check-login')
                .then((response) => {
                    resolve(response.data.data.status == 'success');
                }).catch((error) => {
                    reject(false)
            });
        })
    }
}

export default new Authentication()