class ValidationService {
    isEmptyStr(str) {
        return (str === "" || str === "undefined" || str == null);
    }
}

export default new ValidationService();