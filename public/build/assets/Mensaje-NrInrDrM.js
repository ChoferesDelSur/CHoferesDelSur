import{r as i,o as s,j as p,e as u,c as r,n as t,a as c,f,t as m,l as o,T as g}from"./app-CKfCKN1R.js";/* empty css                                                */const _=c("i",{class:"fas fa-times"},null,-1),d=[_],B={__name:"Mensaje",props:{message:String,color:String},setup(k){const n=e=>{switch(e){case"success":return"fa fa-circle-check";case"error":return"fas fa-times-circle";case"info":return"fas fa-info-circle";case"warning":return"fas fa-exclamation-circle";default:return""}},a=i(!0),l=()=>{a.value=!1};return(e,$)=>(s(),p(g,{name:"message-scale"},{default:u(()=>[a.value?(s(),r("div",{key:0,class:t(["message-container",`bg-${e.$page.props.color}-100 text-${e.$page.props.color}-700`])},[c("i",{class:t(n(e.$page.props.type))},null,2),f(" "+m(e.$page.props.message)+" ",1),e.$page.props.message?(s(),r("button",{key:0,onClick:l,class:"ml-2"},d)):o("",!0)],2)):o("",!0)]),_:1}))}};export{B as _};