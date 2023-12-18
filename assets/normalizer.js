
const operations = {
    phoneNumber: val => ""+val,  //Convert to string
}

/**
 * @param {object} data 
 */
export function normalize(data){
    data = {...data}
    for(const property in operations){
        if(data[property]) data[property] = operations[property](data[property])
    }
    if(data.birthDate == "" || data.birthDate == null) delete data.birthDate
    return data
}