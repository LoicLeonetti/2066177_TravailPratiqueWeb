function validerCC(form) 
{
  

  //Visa               : form[0]
  //Mastercard         : form[1]
  //American express   : form[2]
  //NumÃ©ro de carte    : form[3]
  //Mois d'expiration  : form[4]
  //AnnÃ©e d'expiration : form[5]
  
  modeleVisa=/^4\d{12}(\d{3})?$/;
  modeleMC=/^5[1-5]\d{14}$/;
  modeleAE=/^3[47]\d{13}$/;
  //modeleNAS=/^\d{9}$/;
  
  if (!form[0].checked && !form[1].checked && !form[2].checked ) 
  {
    alert ("Vous devez choisir un type de carte");
    return false;
  }
  numero=new String(form[3].value);
  
  //Validation par carte selon le modÃ¨le
  if ((form[0].checked && !modeleVisa.test(numero)) ||
      (form[1].checked && !modeleMC.test(numero)) ||
      (form[2].checked && !modeleAE.test(numero)) ) 
  {
    alert("IncohÃ©rence entre type de carte et numÃ©ro");
    return false;
  }

  //Validation modulo 10
  var somme=0;
  for( var i=numero.length - 1; i >= 0; i -= 2)
     somme+= (numero.charAt(i) * 1) + numero.charAt(i-1) * 2 - (numero.charAt(i-1) >= 5 ? 9 : 0);
 
  //Si le rÃ©sultat se termine par un zÃ©ro, le modulo 10 est validÃ©
  if (somme%10 != 0) {
     alert ("Le numÃ©ro de carte n'est pas valable");
     return false;
  }
 //VÃ©rification de la date d'expiration
  var aujourdhui = new Date();
  anneeCourante=aujourdhui.getFullYear();
  moisCourant = aujourdhui.getMonth() + 1;
  if (anneeCourante > form[5].value ||
     (anneeCourante == form[5].value && moisCourant > form[4].value)) 
  {
    alert ("Date d'expiration dÃ©passÃ©e");
    return false;
  }
  return true;
}