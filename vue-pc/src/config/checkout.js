import Validator from './validator'
export function formCheck(item){
    // item  需要的校验内容及校验规则
    for(let i = 0; i < item.length; i++){
        if(!Validator.validate(item[i].reg, item[i].val).result){
            return item[i].errMsg ? item[i].errMsg : Validator.validate(item[i].reg, item[i].val).errorMessage
        }
    }
    return { result: true, errorMessage: "校验成功" }
}