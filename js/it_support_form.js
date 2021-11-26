const APPEAL_INFO = 184806 // Запрос на информвцию
const APPEAL_SERVICE = 184807 // Запрос на обслуживание
const APPEAL_CHANGE = 184808
const APPEAL_ACCESS = 184809 // Запрос на предоставление доступа
const MISTAKE = 184810 // Ошибка
const FAILURE = 184811 // Сбой
const USE_ONLY_PERMIT_COMPANY = true
const SUBJECT_DIFFICULT_CHOOISE = 184737 // Раздел предметов обращений - затрудняюсь в выборе
const NEW_USER_CHOISE = 185071 // Раздел предметов обращений - новый сотрудник

var it_support_form = new Vue({
    el: '#it_support_form',
    data: {
        host: "https://24.stim.by",
        loadDataLink: "/local/stim/it_support_stim/api/get_data_for_form.php",
        sendDataLink: "/local/stim/it_support_stim/api/get_data_recived_from_form.php",
        localisation: "",
        allLoadedFotmData: {},
        formLableArr: [],
        fullUserAccessRights: true,
        title: "",
        subject: {
            title: "",
            select: {
                options: [],
                selected: null
            }
        },
        typeAppeal: {
            title: "",
            select: {
                options: [],
                selected: null
            },
            titlePopOver: "",
            popOver: []
        },
        buttonRu: "",
        buttonPl: "",
        footer: {
            textInfoStr1: "",
            textInfoStr2: "",
            buttonCreateText: "",
            buttonClearText: "",
        },
        loadFiles: {
            text: "",
            buttonText: "",
            files: [],
        },
        yourQuestion: {
            text: "",
            question: "",
        },
        objectLink: {
            text: "",
            link: "",
            linkToInstructionName: "",
            linkToInstruction: "",
        },
        needExequte: {
            text: "",
            value: "",
        },
        yourLocation: {
            text: "",
            value: "",
        },
        importantInfo: "",
        requestDescription: {
            text: "",
            value: "",
        },
        mistake: {
            meetBefore: {
                text: "",
                options: [],
                selected: null
            },
            mistakeDescription: {
                text: "",
                value: ""
            },
            earlierSolvingProblem: {
                text: "",
                value: ""
            },
            instructionLink: {
                text: "",
                value: ""
            },
        },
        failure: {
            correctLastTime: {
                text: "",
                value: null,
            },
            problemDescription: {
                text: "",
                value: "",  
            }
        },
        rulesRequest: {
            yourCompany: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },
            },
            yourDepartment: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },           
            },
            userName: {
                textForTag: "",
                textForModal: "",
                select: {
                    selected: [],
                    options: []
                },
                textForComponent: "---- Сделайте ваш выбор ----",
                show: false,
                multiple: false,
                icon: "person-fill",
            },
            userRole: {
                text: "",
                select: {
                    selected: [],
                    options: []
                }, 
            },
            stimCompanies: {
                text: "",
                select: {
                    selected: [],
                    options: []
                }, 
            },
            controleChoiceRoles: {
                buttonText: "",
                linkNoCheckedRole: "",
                linkCheckedRole: "",
            },
            userWithTheSameRole: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },
                show: false,
            },
            phone: {
                text: "",
                value: "",
            },
            notes: {
                text: "",
                value: "",           
            },
            checkBoxModeChoiceRoles: {
                text: "",
                options: [],
                selected: null
            },
        },
        newUser: {
            company: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },
            },
            department: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },           
            },
            position: {
                text: "",
                select: {
                    selected: null,
                    options: []
                },       
            },
            userName: {
                text: "",
                value: "",
            },
            postDomainName: {
                text: "",
                value: "",
            },   
        }
    },
    methods: {
           getData: function() {
               link = this.host + this.loadDataLink + "?CLASS=ALL"
               axios
               .get(link)
               .then( response => { 
                    result = response.data.RESULT
                    this.localisation = result.LANGUAGE_ID
                    this.allLoadedFotmData = result
                    this.fillDaseDataArraysWithInternationalisation()
                    this.fillLabelWirthInternalionalData()
                    //alert( result )
                })
                .catch(  error => {
                  let err = error
                  console.log(error);
              })
           },
           sendDataToServer: function() {
               let link = this.host + this.sendDataLink
               let form = new FormData()
               form.append('CURRENT_USER_ID', this.allLoadedFotmData.CURRENT_USER_ID )
               form.append('CURRENT_USER_PHONE', this.allLoadedFotmData.CURRENT_USER_PHONE )
               if( this.subject.select.selected ) {
                   form.append('SUBJECT_TITLE', this.subject.title )
                   form.append('SUBJECT_SELECTED', this.subject.select.selected )
               }
               if( this.typeAppeal.select.selected ) {
                   form.append('TYPE_APPEAL_TITLE', this.typeAppeal.title )
                   form.append('TYPE_APPEAL_SELECTED', this.typeAppeal.select.selected )
               }
               if( this.yourQuestion.question ) {
                   form.append('YOUR_QUESTION_TITLE', this.yourQuestion.text )
                   form.append('YOUR_QUESTION_QUESTION', this.yourQuestion.question )
               }
               if( this.objectLink.link != "" ) {
                   form.append('OBJ_LINK_TEXT', this.objectLink.text )
                   form.append('OBJ_LINK_LINK', this.objectLink.link )
               }
               if( this.loadFiles.files.length > 0 ) {
                   for( var i=0; i < this.loadFiles.files.length; i++ ) {
                       let file = this.loadFiles.files[i];
                       form.append('files[' + i + ']', file);
                   }
               }
               if( this.needExequte.value )  {
                   form.append('NEED_EXECUTE_TEXT', this.needExequte.text )
                   form.append('NEED_EXECUTE_VALUE', this.needExequte.value )  
               }
               if( this.yourLocation.value )  {
                    form.append('YOUR_LOCATION_TEXT', this.yourLocation.text )
                    form.append('YOUR_LOCATION_VALUE', this.yourLocation.value )  
                }
                if( this.requestDescription.value )  {
                    form.append('DIRECTION_TEXT', this.requestDescription.text )
                    form.append('DIRECTION_VALUE', this.requestDescription.value )  
                    form.append('OBJ_LINK_TEXT', this.objectLink.text )
                    form.append('OBJ_LINK_LINK', this.objectLink.link )
                }
                if( this.typeAppeal.select.selected == APPEAL_ACCESS ) {
                    form.append('RULES_REQUEST_COMPANY_TEXT', this.rulesRequest.yourCompany.text )
                    form.append('RULES_REQUEST_COMPANY_VALUE', this.rulesRequest.yourCompany.select.selected )
                    form.append('RULES_REQUEST_DEPARTMENT_TEXT', this.rulesRequest.yourDepartment.text )
                    form.append('RULES_REQUEST_DEPARTMENT_VALUE', this.rulesRequest.yourDepartment.select.selected )
                    form.append('NOTE_TEXT', this.rulesRequest.notes.text )
                    form.append('NOTE_VALUE', this.rulesRequest.notes.value )
                    form.append('RULES_REQUEST_USER_NAME_TEXT', this.rulesRequest.userName.textForTag )
                    form.append('RULES_REQUEST_USER_NAME_VALUE', this.rulesRequest.userName.select.selected )
                    if( this.rulesRequest.checkBoxModeChoiceRoles.selected == 'user_same_roles') {
                        form.append('RULES_REQUEST_USER_SAME_ROLE_TEXT', this.rulesRequest.userWithTheSameRole.text )
                        form.append('RULES_REQUEST_USER_NAME_SAME_ROLE_VALUE', this.rulesRequest.userWithTheSameRole.select.selected )
                    } else {
                        form.append('RULES_REQUEST_STIM_COMPANY_TEXT', this.rulesRequest.stimCompanies.text )
                        form.append('RULES_REQUEST_STIM_COMPANY_VALUE', this.rulesRequest.stimCompanies.select.selected ) 
                        form.append('RULES_REQUEST_USER_ROLE_TEXT', this.rulesRequest.userRole.text )
                        form.append('RULES_REQUEST_USER_ROLE_VALUE', this.rulesRequest.userRole.select.selected )
                    }
                    form.append('NOTE_TEXT', this.rulesRequest.notes.text )
                    form.append('NOTE_VALUE', this.rulesRequest.notes.value )
                    form.append('CONTACT_PHONE_TEXT', this.rulesRequest.phone.text )
                    form.append('CONTACT_PHONE_VALUE', this.rulesRequest.phone.value )
                }
                if( this.typeAppeal.select.selected == MISTAKE ) {
                    form.append('MISTAKE_DESCRIPTION_TEXT', this.mistake.mistakeDescription.text )
                    form.append('MISTAKE_DESCRIPTION_VALUE', this.mistake.mistakeDescription.value )
                    form.append('MISTAKE_ERLIER_SOLVING_PROBLEM_TEXT', this.mistake.earlierSolvingProblem.text )
                    form.append('MISTAKE_ERLIER_SOLVING_PROBLEM_VALUE', this.mistake.earlierSolvingProblem.value )
                    form.append('MISTAKE_INSTRUCTION_LINK_TEXT', this.mistake.instructionLink.text )
                    form.append('MISTAKE_INSTRUCTION_LINK_VALUE', this.mistake.instructionLink.value )
                }
                if( this.typeAppeal.select.selected == FAILURE ) {
                    form.append('FAILURE_CORRECT_LAST_TIME_TEXT', this.failure.correctLastTime.text )
                    form.append('FAILURE_CORRECT_LAST_TIME_VALUE', this.failure.correctLastTime.value )
                    form.append('FAILURE_PROBLEM_DESCRIPTION_TEXT', this.failure.problemDescription.text )
                    form.append('FAILURE_PROBLEM_DESCRIPTION_VALUE', this.failure.problemDescription.value )
                }
                if( this.ifNewUserAddRequest ) {
                    form.append('COMPANY_TEXT', this.rulesRequest.stimCompanies.text )
                    form.append('COMPANY_VALUE', this.newUser.company.select.selected )
                    form.append('DEPARTMENT_TEXT', this.newUser.department.text )
                    form.append('DEPARTMENT_VALUE', this.newUser.department.select.selected )
                    form.append('POSITION_TEXT', this.newUser.position.text )
                    form.append('POSITION_VALUE', this.newUser.position.select.selected )
                    form.append('USER_TEXT', this.rulesRequest.userName.textForTag )
                    form.append('USER_VALUE', this.newUser.userName.value )
                    form.append('NEED_MAIL_TEXT', this.newUser.postDomainName.text )
                    form.append('NEED_MAIL_VALUE', this.newUser.postDomainName.value )
                    form.append('NOTE_TEXT', this.rulesRequest.notes.text )
                    form.append('NOTE_VALUE', this.rulesRequest.notes.value )
                }
               axios
               .post( link, form)
               //.post( link, JSON.stringify( jsonData ) ) 
               .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                })
                this.relocateToHostStartPage()
            },
            relocateToHostStartPage: function() {
                window.location.href = location.origin
            },
           changeLanguage: function( idLanguage ) {
               this.localisation = idLanguage
               this.changeSubject()
           },
           changeSubject: function() {
               this.fillDaseDataArraysWithInternationalisation()
               this.fillLabelWirthInternalionalData()
               if( this.subject.select.selected && !this.ifExistsSubjectInCurrentLocalisation( this.subject.select.selected ) ) {
                   this.subject.select.selected = null
                   this.typeAppeal.select.selected = null
                   this.clearChoisesisNeed()
               }
           },
           changeAppeal: function() {
               this.clearChoisesisNeed()
            //this.yourQuestion.question = ""
            //this.objectLink.link = ""
           },
           clearChoisesisNeed: function( unconditionalCleaning=false ) {
/*                selectedArr = this.subject.select.options.filter( item=>item.value == this.subject.select.selected )
                if( selectedArr.length == 0 || unconditionalCleaning ) { */
                    //this.subject.select.selected = null
                    //this.typeAppeal.select.selected = null
                    this.yourQuestion.question = ""
                    this.objectLink.linkToInstructionName = ""
                    this.objectLink.link = ""
                    this.objectLink.linkToInstruction = ""
                    this.needExequte.value = ""
                    this.yourLocation.value = ""
                    this.requestDescription.value = ""
                    this.mistake.meetBefore.value = ""
                    this.mistake.mistakeDescription.value = ""
                    this.mistake.earlierSolvingProblem.value = ""
                    this.mistake.instructionLink.value = ""
                    this.mistake.meetBefore.selected = 'meet-before-no'
                    this.failure.correctLastTime.value = null
                    this.failure.problemDescription.value = ""
                    this.rulesRequest.yourCompany.select.selected = null
                    this.rulesRequest.yourDepartment.select.selected = null
                    this.rulesRequest.yourDepartment.select.options = []
                    this.rulesRequest.userRole.select.selected = []
                    this.rulesRequest.stimCompanies.select.selected = []
                    this.rulesRequest.userName.select.selected = []
                    this.rulesRequest.userName.select.options = []
                    this.rulesRequest.userName.show = false
                    this.rulesRequest.userWithTheSameRole.select.selected = null
                    this.rulesRequest.userWithTheSameRole.select.options = []
                    this.rulesRequest.phone.value = ""
                    this.rulesRequest.notes.value = ""
                    this.rulesRequest.checkBoxModeChoiceRoles.selected = 'user_same_roles'
                    this.loadFiles.files = []
/*                 } else if( this.rulesRequest.yourCompany.select.selected ) { // если тип обращения запрос на изменение прав, то сбрасываем не все данные , а только касательно этого хапроса
                    this.rulesRequest.yourCompany.select.selected = null
                    this.rulesRequest.yourDepartment.select.selected = null
                    this.rulesRequest.yourDepartment.select.options = []
                    this.rulesRequest.userRole.select.selected = null
                    this.rulesRequest.userName.select.selected = []
                    this.rulesRequest.userName.select.options = []
                    this.rulesRequest.userName.show = false
                    this.rulesRequest.userWithTheSameRole.select.selected = null
                    this.rulesRequest.userWithTheSameRole.select.options = []
                    this.rulesRequest.phone.value = ""
                    this.rulesRequest.notes.value = ""
                    this.rulesRequest.checkBoxModeChoiceRoles.selected = 'user_same_roles'
                } */
           },
           fillDaseDataArraysWithInternationalisation: function() {
                this.formLableArr = []
                this.allLoadedFotmData.FORMS_LABEL.forEach( label => {
                    //if(  this.localisation == 'ru' || ( this.localisation == 'pl' && label.text_pl != "" ) ) 
                      this.formLableArr.push( { 'value': label.value, 'text': ( this.localisation == 'pl' )? label.text_pl : label.text, 'label_name': label.label_name } )
                })
                this.fillArrSubject()
                this.fillArrtypeAppeal()
                this.mistake.meetBefore.selected = 'meet-before-no'
                this.fillArrCompanies()
                this.fillArrRoles()
                this.fillNewUser()

           },
           computeLabeltextValue: function( arr, valueName ) {
               return arr.filter( item=>item["label_name"]==valueName )[0]["text"]
           },
           fillArrSubject: function() {
              let options = []
              this.allLoadedFotmData.SECTION_AND_SUBJECT.forEach( subject => {
                  if( ( this.fullUserAccessRights || ( !this.fullUserAccessRights && subject.ordinary_user_permition ) ) &&
                    ( this.localisation == 'ru' || ( this.localisation == 'pl' && subject.text_pl != "" ) ) )
                      options.push( { 'value': subject.value, 'text': ( this.localisation == 'pl' )? subject.text_pl : subject.text } )
              })
              this.subject.select.options = options
           },   
           fillArrtypeAppeal: function() {
               if( ! this.subject.select.selected ) return
               let options = []
               this.typeAppeal.popOver = []
               let arrIdAppealsForChiosedSubject = []
               let chiosedSubject = this.allLoadedFotmData.SECTION_AND_SUBJECT.filter( subject=>subject['value'] == this.subject.select.selected )
               arrIdAppealsForChiosedSubject = chiosedSubject[0]["list_available_types"].length>0 ? chiosedSubject[0]["list_available_types"] : []
               this.allLoadedFotmData.APPEAL_TYPE.forEach( type_appeal => {
                   if(  arrIdAppealsForChiosedSubject.includes( type_appeal.value ) ) {
                        options.push( { 'value': type_appeal.value, 'text': ( this.localisation == 'pl' )? type_appeal.text_pl : type_appeal.text } )
                        toopTypeValue = this.allLoadedFotmData.TYPE_TOOLTIP.filter( typeTooltipItem=>typeTooltipItem.appeal_rype_id==type_appeal.value )[0][this.localisation == 'pl' ? "text_pl" : "text"]
                        this.typeAppeal.popOver.push( (this.localisation == 'pl' ? type_appeal.text_pl : type_appeal.text) + " - " + toopTypeValue )
                   }
               })
               this.typeAppeal.select.options = options
           },
           fillArrCompanies: function() {
               let options = []
               let arrPermitedCompanies = []
               this.getArrIdPermitedDepartments().forEach( company=>arrPermitedCompanies.push( company.stim_company ) )
               this.allLoadedFotmData.STIM_COMPANIES.forEach( company => {
                   if( ! USE_ONLY_PERMIT_COMPANY || ( USE_ONLY_PERMIT_COMPANY && arrPermitedCompanies.includes( company.value ) )) options.push( { 'value': company.value, 'text':  company.text } )
               })
               this.rulesRequest.yourCompany.select.options = options
               this.rulesRequest.stimCompanies.select.options = this.allLoadedFotmData.STIM_COMPANIES
           },
           getArrIdPermitedDepartments: function() {
               let arrIdPermitedDepartments = this.allLoadedFotmData.FILTERED_USERS_BY_DEP.filter( department=>department["user_with_rights"].
                                                                                                               includes( this.allLoadedFotmData.CURRENT_USER_ID ) )
                return arrIdPermitedDepartments
           },
           fillArrDepartments: function() {
               this.rulesRequest.yourDepartment.select.selected = null
               let options = []
               let arrAllowedDepartmentsFromSelectedCompany = this.allLoadedFotmData.FILTERED_USERS_BY_DEP.filter( drpartment=>drpartment.stim_company==this.rulesRequest.yourCompany.select.selected )
               arrAllowedDepartmentsFromSelectedCompany = arrAllowedDepartmentsFromSelectedCompany.filter( drpartment=>drpartment.user_with_rights.includes( this.allLoadedFotmData.CURRENT_USER_ID ) )
               arrAllowedDepartmentsFromSelectedCompany.forEach( departmet=>options.push( { 'value': departmet.id_department, text: departmet.text }) )
               this.rulesRequest.yourDepartment.select.options = options
           },
           changeYourCompany: function() {
               this.fillArrDepartments()
           },
           fillArrAccessUsers: function() {
               this.rulesRequest.userName.select.options = this.allLoadedFotmData.FILTERED_USERS_BY_DEP
                                                               .filter( drpartment=>drpartment.id_department == this.rulesRequest.yourDepartment.select.selected)[0]['users']
                this.rulesRequest.userName.show = true
           },

           fillArrUsersSameRoles: function() {
            this.rulesRequest.userWithTheSameRole.select.options = this.allLoadedFotmData.FILTERED_USERS_BY_DEP
                                                            .filter( drpartment=>drpartment.id_department == this.rulesRequest.yourDepartment.select.selected)[0]['users']
             this.rulesRequest.userWithTheSameRole.show = true
            },

           changeYourDepartment: function() {
               this.fillArrAccessUsers()
               this.fillArrUsersSameRoles()
           },
           saveChoisedUsers: function( param ) {
               this.rulesRequest.userName.select.selected = param
           },
           saveDataFromPopupRoles: function( param ) {
               //alert( param )
               this.rulesRequest.stimCompanies.select.selected = param.companies
               this.rulesRequest.userRole.select.selected = param.roles
           },
           fillArrRoles: function() {
               let options = []
               let option_item
               this.allLoadedFotmData.ROLE_GROUPS
               .forEach( roleGroup=> { 
                                         option_item = { 'label': "", options: [] }
                                         option_item.label =  this.localisation=='ru' ? roleGroup.text : roleGroup.test_pl
                                         //options.push( { 'label': ( this.allLoadedFotmData.LANGUAGE_ID=='RU' ? roleGroup.text : roleGroup.test_pl) } )
                                         this.allLoadedFotmData.ROLES
                                         .filter( role=>role.role_groups==roleGroup.value )
                                         .forEach( role=> {
                                            option_item.options.push( { 'value': role.value, text: ( this.localisation=='ru' ? role.text : role.test_pl)  } )
                                         })
                                         options.push( option_item )
                })
                this.rulesRequest.userRole.select.options = options
                this.rulesRequest.checkBoxModeChoiceRoles.selected = 'user_same_roles'
                this.rulesRequest.phone.value = this.allLoadedFotmData.CURRENT_USER_PHONE;
           },
           fillNewUser: function() {
               this.newUser.company.select.options = this.allLoadedFotmData.STIM_COMPANIES
               this.newUser.department.select.options = this.allLoadedFotmData.BITRIX_DEPARTMENTS
               this.newUser.position.select.options = this.allLoadedFotmData.BITRIX_POSITIONS
           },
           fillLabelWirthInternalionalData: function() {
               this.title = this.computeLabeltextValue( this.formLableArr, "TITLE")
               this.buttonRu = this.computeLabeltextValue( this.formLableArr, "BUTTON_RU")
               this.buttonPl = this.computeLabeltextValue( this.formLableArr, "BUTTON_PL")

               this.subject.title = this.computeLabeltextValue( this.formLableArr, "SUBJECT")
               this.typeAppeal.title = this.computeLabeltextValue( this.formLableArr, "TYPE_APPEAL")
               this.typeAppeal.titlePopOver = this.computeLabeltextValue( this.formLableArr, "HOW_DEFINE")

               this.footer.textInfoStr1 = this.computeLabeltextValue( this.formLableArr, "FORM_INFO_1")
               this.footer.textInfoStr2 = this.computeLabeltextValue( this.formLableArr, "FORM_INFO_2")
               this.footer.buttonCreateText = this.computeLabeltextValue( this.formLableArr, "BUTTON_CREATE")
               this.footer.buttonClearText = this.computeLabeltextValue( this.formLableArr, "BUTTON_CLEAR")

               this.loadFiles.text = this.computeLabeltextValue( this.formLableArr, "SCREENSHOT")
               this.loadFiles.buttonText = this.computeLabeltextValue( this.formLableArr, "SCREENSHOT_BUTTON_TEXT")

               this.yourQuestion.text = this.computeLabeltextValue( this.formLableArr, "YOUR_QUESTION")

               this.objectLink.text = this.computeLabeltextValue( this.formLableArr, "OBJ_LINK")
               this.objectLink.linkToInstructionName = this.computeLabeltextValue( this.formLableArr, "HOW_GET_LINK")
               this.objectLink.linkToInstruction = this.computeLabeltextValue( this.formLableArr, "LINK_INSTRUCTION")

               this.needExequte.text = this.computeLabeltextValue( this.formLableArr, "NEED_EXEQUTE")
               this.yourLocation.text = this.computeLabeltextValue( this.formLableArr, "YOUR_LOCATION")

               this.importantInfo = this.computeLabeltextValue( this.formLableArr, "IMPORTANT_INFO")

               this.requestDescription.text = this.computeLabeltextValue( this.formLableArr, "REQUEST_DESCRIPTION")

               this.mistake.meetBefore.text = this.computeLabeltextValue( this.formLableArr, "ERROR_MEET_BEFORE")
               this.mistake.meetBefore.options = [
                   { text: this.computeLabeltextValue( this.formLableArr, "BUTTON_NO"), value: 'meet-before-no' },
                   { text: this.computeLabeltextValue( this.formLableArr, "BUTTON_YES"), value: 'meet-before-yes'},
               ]
               this.mistake.mistakeDescription.text = this.computeLabeltextValue( this.formLableArr, "MISTAKE_DESCRIPTION")
               this.mistake.earlierSolvingProblem.text = this.computeLabeltextValue( this.formLableArr, "EARLIER_SOLVING_PROBLEM")
               this.mistake.instructionLink.text = this.computeLabeltextValue( this.formLableArr, "INSTRUCTION_LINK")

               this.failure.correctLastTime.text = this.computeLabeltextValue( this.formLableArr, "CORRECT_LAST_TIME")
               this.failure.problemDescription.text = this.computeLabeltextValue( this.formLableArr, "PROBLEM_DESCRIPTION")

               this.rulesRequest.yourCompany.text = this.computeLabeltextValue( this.formLableArr, "YOUR_COMPANY")
               this.rulesRequest.yourDepartment.text = this.computeLabeltextValue( this.formLableArr, "YOUR_DEPARTMENT")
               this.rulesRequest.checkBoxModeChoiceRoles.text = this.computeLabeltextValue( this.formLableArr, "LABEL_CRECKBOX_TYPE_CHOICE_ROLES")
               this.rulesRequest.checkBoxModeChoiceRoles.options = [
                   { text: this.computeLabeltextValue( this.formLableArr, "MANUAL_ROLES"), value: 'manual_roles' },
                   { text: this.computeLabeltextValue( this.formLableArr, "USER_SAME_ROLES"), value: 'user_same_roles'},
               ]
               this.rulesRequest.userRole.text = this.computeLabeltextValue( this.formLableArr, "USER_ROLE")
               this.rulesRequest.stimCompanies.text = this.computeLabeltextValue( this.formLableArr, "STIM_COMPANIES")
               this.rulesRequest.userName.textForTag = this.computeLabeltextValue( this.formLableArr, "USER_FIO")
               this.rulesRequest.userName.textForModal = this.computeLabeltextValue( this.formLableArr, "TITLE_MODAL_REQUEST_USERS")
               this.rulesRequest.userWithTheSameRole.text = this.computeLabeltextValue( this.formLableArr, "USER_SAME_ROLES")
               this.rulesRequest.controleChoiceRoles.buttonText = this.computeLabeltextValue( this.formLableArr, "BUTTON_CHECK_ROLES")
               this.rulesRequest.controleChoiceRoles.linkNoCheckedRole = this.computeLabeltextValue( this.formLableArr, "LINK_NO_CHECKED_ROLES")
               this.rulesRequest.controleChoiceRoles.linkCheckedRole = this.computeLabeltextValue( this.formLableArr, "LINK_CHECKED_ROLES")
               this.rulesRequest.phone.text = this.computeLabeltextValue( this.formLableArr, "CONTACT_PHONE")
               this.rulesRequest.notes.text = this.computeLabeltextValue( this.formLableArr, "NOTES")
               this.newUser.department.text = this.computeLabeltextValue( this.formLableArr, "STIM_DEPARTMRNTS")
               this.newUser.position.text = this.computeLabeltextValue( this.formLableArr, "POSITION")
               this.newUser.postDomainName.text = this.computeLabeltextValue( this.formLableArr, "NEED_MAIL")
           },
        //    ctrateAppealSupport: function() {
        //        alert("Нажата кнопка создания")
        //    },
           formClear: function() {
                this.clearChoisesisNeed( true )
           },
           ifExistsSubjectInCurrentLocalisation: function( idSubject ) {
               let filteredArrSubjects = this.subject.select.options.filter( item=>item.value == idSubject )
               return filteredArrSubjects.length>0
           },
    },
    filters: {

    },
    computed: {
        ifAppealInfo: function() { return this.typeAppeal.select.selected == APPEAL_INFO },
        ifAppealService: function() { return this.typeAppeal.select.selected == APPEAL_SERVICE },
        ifAppealChange: function() { return this.typeAppeal.select.selected == APPEAL_CHANGE },
        ifAppealAccess: function() { return this.typeAppeal.select.selected == APPEAL_ACCESS },
        ifFooterShow: function() { return this.subject.select.selected==SUBJECT_DIFFICULT_CHOOISE || this.typeAppeal.select.selected ||
                                    ( this.ifNewUserAddRequest && this.newUser.company.select.selected && this.newUser.department.select.selected && this.newUser.position.text 
                                        && this.newUser.userName.value!="") },
        ifMistake: function() { return this.typeAppeal.select.selected == MISTAKE },
        ifFailure: function() { return this.typeAppeal.select.selected == FAILURE },
        ifTypeAppeal: function() { return this.ifSelectSubject && this.typeAppeal.select.options.length>0 },
        ifRigthAccess: function() { return this.typeAppeal.select.selected == APPEAL_ACCESS },
        ifNewUserAddRequest: function() { return this.subject.select.selected == NEW_USER_CHOISE },
        selectedSubjectArrFulVersion: function() { 
            if( ! this.subject.select.selected ) return false
            else return  this.allLoadedFotmData.SECTION_AND_SUBJECT.filter( subject=>subject["value"] == this.subject.select.selected )
        },
        ifListAvailableTypiesIsEmpty: function() {
            let condition = this.selectedSubjectArrFulVersion && this.selectedSubjectArrFulVersion.length>0 
            return !(condition ? this.selectedSubjectArrFulVersion[0]["list_available_types"].length>0 : false)
        },
        ifSelectSubject: function() { return this.subject.select.selected ? true : false },
        dataForPopupRoles: function() {
            return {
                actionTitle: this.rulesRequest.userRole.text,
                buttonShowModal: {
                    text: this.rulesRequest.controleChoiceRoles.buttonText
                },
                linkShowSelectedCompaniesAndRoles: {
                    linkNoCheckedRoleText: this.rulesRequest.controleChoiceRoles.linkNoCheckedRole,
                    linkCheckedRoleText: this.rulesRequest.controleChoiceRoles.linkCheckedRole
                },
                objForSelectRoles: this.rulesRequest.userRole,
                objForSelectCopmpanies: this.rulesRequest.stimCompanies,
            }
        }
/*         selectedUsers: function() { 
            return this.rulesRequest.userName.select.selected ? this.rulesRequest.userName.select.options.filter( user=>user.value == this.rulesRequest.userName.select.selected )[0].text : ""
         } */
    },
    mounted: function () {
        this.getData()
    },
    created: function () {
       //this.getData()
  },
})