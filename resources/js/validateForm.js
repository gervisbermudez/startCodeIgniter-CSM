class VueForm {
    fields = null;
    debug = true;
    errors = [];
    valid = false;
    patterns = {
        name: /^[a-zA-ZáÁéÉíÍóÓúÚñÑ\s\'\´]*$/,
        email: /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*(\+[a-zA-Z0-9-]+)?@[a-zA-Z0-9]+[a-zA-Z0-9.-]+\.[a-zA-Z]{1,6}$/i,
        number: /^[0-9]*$/,
        date: /^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/,
        symbols: /[-!$%^&*()_+|~=`{}\[\]:";'<>?,.\/]/,
        //Must contain more than 5 character, a-z numbers and _ - character
        username: /^[A-Za-z]*[A-Za-z][A-Za-z0-9-_]{5,}$/,
        //Can contain numbers and letters 
        alphanumeric: /^[a-z0-9]+$/i,
        // Password need to be a minimum of 8 characters include a special character and at least one capital letter
        password: /^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[#?!@$%^&*\-_]).{8,}$/
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
              type: fields[field].type || "text",
              length: fields[field].length || 0,
              valid: fields[field].valid || true,
              touched: false,
              errorText: fields[field].errorText || "",
              customPattern: fields[field].customPattern || null,
              validateWith: null,
              maxLength: fields[field].maxLength || null,
              minLength: fields[field].minLength || null,
              readOnlyValue: null,
            };
        }
    }
    /**
     * Funcion que valida un campo en especifico.
     * @param field campo a validar
     */
    validateField(field) {
        /**
         *  Lo marca como tocado o alterado 
         */
        this.fields[field].touched = true;

        if (this.fields[field].readOnlyValue && (this.fields[field].readOnlyValue == this.fields[field].value)) {
            this.markFieldAsValid(field);
            this.fields[field].errorText = '';
            return true;
        }

        /**
         * Realizar la validacion basica
         */
        if (!this.basicFieldValidation(field)) {
            return false;
        }

        /**
         * Marcar el campo como valido
         */
        this.markFieldAsValid(field);
        this.fields[field].errorText = '';

        /** 
         * Verifica si el campo tiene una funcion de validacion personalizada y la ejecuta
         */
        if (this.fields[field].validateWith && (typeof this.fields[field].validateWith === 'function') && !this.fields[field].validateWith(field)) {
            this.markFieldAsInvalid(field);
            this.fields[field].errorText = this.getErrorText(field);
            return false;
        }

        return true;
    }

    /**
     * Realiza las siguientes validaciones: 
     * Contiene un valor
     * Requerido
     * Minlength
     * Maxlength
     * Expresion regular
     * @param {string} field campo a validar
     */
    basicFieldValidation(field) {

        /**
         * Verifica que si el campo es requerido contenga un valor 
         */
        if (this.fields[field].required && !this.fields[field].value) {
            this.markFieldAsInvalid(field);
            this.fields[field].errorText = 'This field is required';
            return false;
        }

        /**
         * Verifica que el campo tenga un minLength valido
         */
        if (this.fields[field].minLength && !this.minLength(this.fields[field].value, this.fields[field].minLength)) {
            this.markFieldAsInvalid(field);
            this.fields[field].errorText = 'Minlength required: ' + this.fields[field].minLength;
            return false;
        }

        /**
         * Verifica que el campo tenga un maxLength valido
         */
        if (this.fields[field].maxLength && !this.maxLength(this.fields[field].value, this.fields[field].maxLength)) {
            this.markFieldAsInvalid(field);
            this.fields[field].errorText = this.fields[field].errorText = 'Maxlength required: ' + this.fields[field].maxLength;
            return false;
        }

        /**
         * Verifica que si contiene un valor sea válido
         */
        if (!this.patternValidation(field)) {
            this.markFieldAsInvalid(field);
            return false;
        }

        return true;
    }

    validate() {
        /** Recorre los campos del form */
        this.errors = [];
        for (const field in this.fields) {
            this.fields[field].touched = true;
            this.fields[field].required && this.validateField(field);
        }
        return this.valid;
    }
    /**
     * Valida el valor del campo contra una expresion regular
     */
    patternValidation(field, length = 0) {
        var valid = true;
        var control = this.fields[field];
        /**
         * Varifica que exista un custom patern
         */
        if (this.fields[field].customPattern) {
            valid = this.fields[field].customPattern.test(control.value);
        } else {
            switch (control.type) {
                case 'name':
                    valid = this.nameValidation(control.value, length);
                    this.fields[field].errorText = valid ? '' : 'Only letters in this field';
                    break;
                case 'number':
                    valid = this.numValidation(control.value, length);
                    this.fields[field].errorText = valid ? '' : 'Only numbers in this field';
                    break;
                case 'email':
                    valid = this.emailValidation(control.value);
                    this.fields[field].errorText = valid ? '' : 'Email don`t have the correct format: example@domain.com';
                    break;
                case 'cuit':
                    valid = this.CUILValidation(control.value);
                    this.fields[field].errorText = valid ? '' : 'Cuit don`t have the correct format: ##-########-#';
                    break;
                case 'date':
                    valid = this.dateValidation(control.value);
                    this.fields[field].errorText = valid ? '' : 'Correct date format: YYYY-MM-DD';
                    break;
                case 'password':
                    valid = this.passwordValidator(control.value);
                    this.fields[field].errorText = valid ? '' : 'Include a special character, number and at least one capital and lower letter';
                    break;
                default:
                    /**
                     * Varificar si existe una expresion regular global y testear
                     */
                    if (typeof this.patterns[control.type] != 'undefined') {
                        valid = this.patterns[control.type].test(control.value);
                        this.fields[field].errorText = valid ? '' : field.charAt(0).toUpperCase() + field.slice(1) + ' is invalid';
                    } else {
                        valid = false;
                    }
                break;
            }
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
        return this.fields[field].errorText ? this.fields[field].errorText : field.charAt(0).toUpperCase() + field.slice(1) + ' is invalid';
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
     * 
     * @param {string} field 
     */
    passwordValidator(value) {
        return this.patterns.password.test(value);
    }

    /**
     * Validar que un string v valide que no sea mayor que m (maximo)
     * @param {any} value valor string a validar
     * @param {number} m longitud maxima a validar
     */
    maxLength(value, m) {
        if (!value || (value && value.length > m)) {
            return false;
        }
        return true;
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} value value
     */
    emailValidation(value) {
        return this.patterns.email.test(value);
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} value value
     */
    nameValidation(value, l = 1) {
        if (this.patterns.name.test(value) == false || !this.minLength(value, l)) {
            return false;
        }
        return true;
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} value value
     */
    dateValidation(value) {
        return this.patterns.date.test(value);
    }
    /**
     * Validar que un string tiene formato definido por un patron
     * @param {any} value value
     */
    numValidation(value, l = 0) {
        if (this.patterns.number.test(value) == false || !this.minLength(value, l)) {
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
        this.errors.push(field);
    }
    /**
     * Marcar como valido el campo
     * @param {string} field nombre del campo
     */
    markFieldAsValid(field) {
        this.fields[field].valid = true;
        const index = this.errors.indexOf(field);
        if (index > -1) {
            this.errors.splice(index, 1);
        }
    }
}