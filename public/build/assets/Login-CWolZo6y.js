import{j as b,s as _,o,h as l,b as s,t as k,e as C,r as V,d as i,u as a,w as p,F as d,H as S,n as f,k as v,g as h}from"./app-B_a5flZ5.js";import{A as B,_ as z}from"./PrimaryButton-B3TG1pgl.js";import{_ as g,a as w}from"./InputLabel-DCo5VsiC.js";import{_ as A}from"./Mensaje-BLox0mlw.js";/* empty css                                                */const $={class:"text-sm text-red-600"},x={__name:"InputError",props:{message:String},setup(n){return(t,m)=>b((o(),l("div",null,[s("p",$,k(n.message),1)],512)),[[_,n.message]])}},I={class:"mb-4"},P={class:"flex items-center"},j={class:"mb-4"},q={class:"flex items-center"},F={class:"relative"},M={class:"flex items-center justify-end mt-4"},L={__name:"Login",props:{canResetPassword:Boolean,status:String},setup(n){const t=C({usuario:"",password:"",remember:!0}),m=()=>{t.transform(c=>({...c,remember:!!t.remember})).post(route("inicioSesion"),{onFinish:()=>t.reset("password")})},r=V(!1),y=()=>{r.value=!r.value};return(c,e)=>(o(),l(d,null,[i(a(S),{title:"Iniciar sesión"}),i(B,null,{default:p(()=>[s("div",null,[e[2]||(e[2]=s("h2",{class:"text-black text-2xl text-center font-semibold p-2"},"Iniciar Sesión",-1)),e[3]||(e[3]=s("div",{class:"flex justify-center"},[s("img",{src:"https://i.postimg.cc/Pqxt4sy5/busAzul.gif",alt:"Bus Amarillo",class:"w-32 h-32"})],-1)),i(A),e[4]||(e[4]=s("div",{class:"p-4 mb-0 text-sm text-justify rounded-lg"},[s("span",{class:""},"Bienvenido al sistema de control y gestión de la empresa Sociedad Cooperativa de Choferes del Sur S.C.L. Para acceder a la información es necesario que inicie sesión.")],-1))]),s("form",{onSubmit:h(m,["prevent"])},[s("div",I,[s("div",P,[e[5]||(e[5]=s("i",{class:"fa fa-user mr-2","aria-hidden":"true"},null,-1)),s("div",null,[i(g,{for:"usuario",value:"Usuario"})])]),i(w,{id:"usuario",modelValue:a(t).usuario,"onUpdate:modelValue":e[0]||(e[0]=u=>a(t).usuario=u),type:"text",class:"mt-1 block w-full",required:"",autofocus:"",autocomplete:"usuario"},null,8,["modelValue"]),i(x,{class:"mt-2",message:a(t).errors.email},null,8,["message"])]),s("div",j,[s("div",q,[e[6]||(e[6]=s("i",{class:"fa fa-unlock-alt mr-2","aria-hidden":"true"},null,-1)),s("div",null,[i(g,{for:"password",value:"Contraseña"})])]),s("div",F,[i(w,{id:"password",modelValue:a(t).password,"onUpdate:modelValue":e[1]||(e[1]=u=>a(t).password=u),type:r.value?"text":"password",class:"mt-1 block w-full pr-10",required:"",autocomplete:"current-password"},null,8,["modelValue","type"]),s("button",{type:"button",class:"absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none",onClick:y},[s("i",{class:f(["fa",r.value?"fa-eye-slash":"fa-eye"])},null,2)])]),i(x,{class:"mt-2",message:a(t).errors.password},null,8,["message"])]),s("div",M,[i(z,{class:f({"opacity-85":a(t).processing,"opacity-100":!a(t).processing}),disabled:a(t).processing},{default:p(()=>[a(t).processing?(o(),l(d,{key:0},[e[7]||(e[7]=s("svg",{class:"animate-spin h-5 w-5 mr-3 text-white",viewBox:"0 0 24 24"},[s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4",fill:"none"}),s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A8.005 8.005 0 0112 4.245v3.8C9.29 8.674 7.326 10.404 6 12.291zM16 8.674v3.8c1.326 1.887 3.29 3.617 6 4.246zm-3.764 7.764A8.005 8.005 0 0112 19.755v-3.8c2.71-1.628 4.674-3.358 6-5.245z"})],-1)),e[8]||(e[8]=v(" Iniciando... "))],64)):(o(),l(d,{key:1},[e[9]||(e[9]=v(" Iniciar sesión ")),e[10]||(e[10]=s("i",{class:"fa fa-sign-in","aria-hidden":"true",style:{"margin-left":"0.5rem"}},null,-1))],64))]),_:1},8,["class","disabled"])])],32)]),_:1})],64))}};export{L as default};