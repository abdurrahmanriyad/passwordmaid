import axios from "axios/index";

class Project {
    async getProjects() {
        return new Promise(resolve => {
            axios.get('/ajax/auth/projects')
                .then((response) => {
                    return (response.data.data === 'undefined') ? resolve(null) : resolve(response.data.data);
                });
        })
    }
}

export default new Project();