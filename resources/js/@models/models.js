import {Model, Collection} from 'vue-mc'

export class Employee extends Model   {
    // Default attributes that define the "empty" state.
    defaults() {
        return {
            id:0,
            name:'',
            name_ar:'',
            surname:'',
            surname_ar:'',
            birthdate:'',
            birthplace:'',
            email:'',
            address:'',
            phone:'',
            NIN:'',
            NCN:'',
            CNAS:'',
            card_issue_date:'',
            card_issue_place:'',
            father_name_ar: '',
            mother_full_name_ar: '',
            birth_city_id:  0,
            card_issued_city_id:  0,
        }
    }

    // Attribute mutations.
    mutations() {
        return {
            id:   (id) => Number(id) || null,
            name: String,
            done: Boolean,
            name_ar:String,
            surname:String,
            surname_ar:String,
            birthdate:String,
            birthplace:String,
            email:String,
            address:String,
            phone:String,
            NIN:String,
            NCN:String,
            CNAS:String,
            card_issue_date:String,
            card_issue_place:String,
            father_name_ar: String,
            mother_full_name_ar: String,
            birth_city_id:   (id) => Number(id) || null,
            card_issued_city_id:   (id) => Number(id) || null,

        }
    }

}

class EmployeeList extends Collection {

    // Model that is contained in this collection.
    model() {
        return Employee;
    }

    // Default attributes
    defaults() {
        return {
            orderBy: 'name',
        }
    }


}
