/**
 * 2018/01/23
 * mark.long
 * FUN：
 * import Validate from 'validator';路径
 *  eg：mobile验证的变量  mobileValue要验证的值
 *  const validate = Validate.validate('mobile', mobileValue);
 *  const { result,errorMessage } = validate;
 */

export default class Validator{
    static get REGX() {
      return {
        'amount':{
          'reg': /^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$/,
          'errorMessage': '请填写正确的金额'
        },
        'mobile':{
          'reg': /^1[3|4|5|7|8|9]\d{9}$/,
          'errorMessage': '请输入正确的手机号码'
        },
        'otp':{
          'reg': /^\d{6}$/,
          'errorMessage': '错误的验证码'
        },
        'name':{
          'reg': /^[\u4e00-\u9fa5]([\u4e00-\u9fa5]{0,24}\u00b7{0,1}[\u4e00-\u9fa5]{1,24})+$/,
          'errorMessage': '请输入正确的姓名'
        },
        'liaisonName':{
          'reg': /^[\u4e00-\u9fa5]([\u4e00-\u9fa5]{0,24}\u00b7{0,1}[\u4e00-\u9fa5]{1,24})+$/,
          'errorMessage': '请输入正确的联系人姓名'
        },
        'cnid':{
          'reg': /^(\d|x|X){18}$/,
          'errorMessage': '请输入正确的身份证号码'
        },
        'company_name':{
          // 'reg': /^[^@#\$%\^&\*]{2,50}$/,
          'reg': /^[（）()a-zA-Z0-9_\u4e00-\u9fa5 -]{2,50}$/,
          'errorMessage': '请输入正确的公司名称'
        },
        'telephone':{
          'reg': /^(?:010|02\d|0[3-9]\d{2})\-?\d{7,8}(?:\-\d{1,4}$)?/,
          'errorMessage': '请输入正确的公司电话'
        },
        'address':{
          'reg': /^[^@#\$%\^&\*]{2,50}$/,
          'errorMessage': '请输入正确的地址'
        },
        'qq':{
          'reg': /^\d{5,20}$/,
          'errorMessage': '请输入正确QQ号码'
        },
        'noData': {
          'reg': /^\S+$/,
          'errorMessage': '不能为空'
        },
        'bankCard':{
          'reg': /^(\d{13,19})$/,
          'errorMessage': '请输入正确的银行卡号'
        }
      }
    }
    static validate(name, value){
        var validateResult = { result:true, errorMessage:'' },
        regs = this.REGX;
        if (regs.hasOwnProperty(name)) {
            var result = null != value && regs[name]['reg'].test(value);
        var errorMessage = regs[name]['errorMessage'];
  
          validateResult = {result:result, errorMessage:errorMessage};
        }
        return validateResult;
    }
  }