class VueForm {
    fields = null;
    debug = false;
    errors = [];
    valid = false;
    patterns = {
        name: /^[a-zA-ZáÁéÉíÍóÓúÚñÑ\s\'\´]*$/,
        email: /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*(\+[a-zA-Z0-9-]+)?@[a-zA-Z0-9]+[a-zA-Z0-9.-]+\.[a-zA-Z]{1,6}$/i,
        number: /^[0-9]*$/,
        date: /^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/,
        symbols: /^[\w.]+$/i,
        alphanumeric: /^[ a-z0-9A-Z]*$/g
    }
    constructor(fields) {
        this.generateForm(fields);
    }
    generateForm(fields) {
        if (!fields) {
            return;
        }
        this.fields = fields;
        for (const field in fields) {
            this.fields[field] = {
                value: fields[field].value || null,
                required: fields[field].required || false,
                type: fields[field].type || 'text',
                length: fields[field].length || 0,
                valid: fields[field].required || true,
                touched: false,
                errorText: fields[field].errorText || '',
                customPattern: fields[field].customPattern || null,
                validateWith: null,
                maxLength: fields[field].maxLength || null,
            }
        }
    }
    /**
     * Funcion que valida un campo en especifico.
     * @param field campo a validar
     */
    validateField(field) {
        /** Lo marca como tocado o alterado */
        this.fields[field].touched = true;
        /**Verifica si el campo tiene una funcion de validacion personalizada */
        if (this.fields[field].validateWith && (typeof this.fields[field].validateWith === 'function')) {
            if (this.fields[field].validateWith(this.fields[field].value)) {
                this.markFieldAsInvalid(field);
                this.fields[field].errorText = this.getErrorText(field);
                return false;
            } else {
                this.markFieldAsValid(field);
                this.fields[field].errorText = this.getErrorText(field);
                return true;
            }
        } else {
            /**
             * Verifica que el campo sea requerido y que tenga un maxLength
             */
            if (this.fields[field].required && !!this.fields[field].maxLength) {
                if (!this.maxLength(this.fields[field].value, this.fields[field].maxLength)) {
                    this.markFieldAsInvalid(field);
                    this.fields[field].errorText = this.getErrorText(field);
                    return false;
                }
            }

            /**
             * Verifica que si el campo es requerido contenga un valor 
             */
            if (this.fields[field].required && !this.fields[field].value) {
                this.markFieldAsInvalid(field);
                this.fields[field].errorText = this.getErrorText(field);
                return false;
            }

            /**
             * Verifica que si contiene un valor sea válido
             */
            else if (this.fields[field].value && !this.fieldTypeValidation(field)) {
                this.markFieldAsInvalid(field);
                this.fields[field].errorText = this.getErrorText(field);
                return false;
            } else {
                this.markFieldAsValid(field);
                this.fields[field].errorText = this.getErrorText(field);
                return true;
            }

        }
    }
    validate() {
        /** Recorre los campos del form */
        this.errors = [];
        for (const field in this.fields) {
            this.fields[field].touched = true;
            if (!this.validateField(field)) {
                this.errors.push(field);
            }
        }
        this.valid = this.errors.length ? false : true;
        this.debug ? console.log(this, this.valid) : null;
        return this.valid;
    }
    /**
     * @todo: crear custom types con validaciones
     */
    fieldTypeValidation(field, length = 0) {
        var valid = true;
        var control = this.fields[field]
        switch (control.type) {
            case 'name':
                valid = this.nameValidation(control.value, length);
                break;
            case 'number':
                valid = this.numValidation(control.value, length);
                break;
            case 'email':
                valid = this.emailValidation(control.value);
                break;
            case 'cuit':
                valid = this.CUILValidation(control.value);
                break;
            case 'date':
                valid = this.dateValidation(control.value);
                break;
            default:
                /** Texto libre - Para este form no es necesario validar el pattern ni length */
                break;
        }
        if (valid) {
            this.markFieldAsValid(field);
        } else {
            this.markFieldAsInvalid(field);
        }
        return valid;
    }
    /**
     * @todo regresar mensajes de error personalizados por cada tipo de campo
     */
    getErrorText(field) {
        if (this.fields[field].valid) {
            return '';
        }
        return 'Campo invalido'
    }
    /** VALIDATION LIST */
    /**
     * Validar si un string tienen el formato de CUIT
     * @param {any} v string cuit 
     */
    CUILValidation(v) {
        if (v) {
            v = v.toString().replace(/-/g, "");
            let r = 0, a = v.split(''), m = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
            if (v && v.length == 11) {
                for (let i = 0; i <= 9; i++) { r += Number(a[i]) * m[i]; }
                r = (11 - (r % 11));
                if (((r == 11) ? 0 : ((r == 10) ? 9 : r)) == Number(a[10])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * Validar que un string v valide que no sea menor que m
     * @param {any} v string a validar
     * @param {number} m longitud a validar
     */
    minLength(v, m) {
        if (!v || (v && v.length < m)) {
            return false;
        }
        return true;
    }
    /**
     * Validar que un string v valide que no sea mayor que m (maximo)
     * @param {any} v valor string a validar
     * @param {number} m longitud maxima a validar
     */
    maxLength(v, m) {
        if (!v || (v && v.length > m)) {
            return false;
        }
        return true;
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} v value
     */
    emailValidation(v) {
        return this.patterns.email.test(v);
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} v value
     */
    nameValidation(v, l = 1) {
        if (this.patterns.name.test(v) == false || !this.minLength(v, l)) {
            return false;
        }
        return true;
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} v value
     */
    dateValidation(v) {
        return this.patterns.date.test(v);
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} v value
     */
    numValidation(v, l = 0) {
        if (this.patterns.number.test(v) == false || !this.minLength(v, l)) {
            return false;
        }
        return true;
    }
    /**
     * Marcar como invalido el campo
     * @param {string} field nombre del campo
     */
    markFieldAsInvalid(field) {
        this.fields[field].valid = false;
    }
    /**
     * Marcar como valido el campo
     * @param {string} field nombre del campo
     */
    markFieldAsValid(field) {
        this.fields[field].valid = true;
    }
}