import{_ as fa,a as ba,b as ha,c as xa,d as va,e as Sa,f as _a,g as Ea}from"./ReportesUltimasCorridas-r7Oua6kR.js";import{_ as Ca}from"./ServicioLayout-U0_O4Lk5.js";import{_ as ya}from"./ReporteOperadoresSinTrabajar-FjMoaBJ9.js";import{K as ra,r as W,o as g,h as f,y as E,b as n,t as C,j as L,z as F,u as v,L as K,F as y,n as A,k as na,I as ia,i as $a,a as wa,w as Ua,d as x}from"./app-CYjsmDsa.js";import{S as U}from"./sweetalert2.esm.all-BGf-Fe8G.js";import{u as N,w as sa}from"./xlsx-DjuO7_Ju.js";import{E as da}from"./jspdf.plugin.autotable-Xt2Ils1H.js";/* empty css                                                               */const Da={class:"text-lg font-bold"},Oa={class:"flex flex-wrap gap-4 mb-3"},ka={class:"flex flex-wrap space-x-3 mb-2"},La=["value"],Fa={class:"flex flex-wrap space-x-3"},Na=["onClick"],Ta={__name:"ReportesMultasDominicales",props:{message:{String,default:""},color:{String,default:""},unidad:{type:Object,default:()=>({})},entrada:{type:Object,default:()=>({})},corte:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})}},setup(R){const o=ra({unidad:null,operador:null}),S=W([]);let m=1;const P=[{titulo:"Multas Dominicales"}],B=[{tipo:"pdf",texto:"Generar PDF",clase:"bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-pdf"},{tipo:"excel",texto:"Generar Excel",clase:"bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-excel"}],D=p=>{if(!p)return"Sin registro";const[d,c]=p.split(":");return`${d}:${c}`},I=async p=>{const d=route("reporte.multasDominicales",{semana:p});try{const c=await ia.get(d);S.value=Object.values(c.data)}catch(c){console.error("Error al obtener los datos:",c.response?c.response.data:c.message),U.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}},V=async(p,d,c,u)=>{if(!m){U.fire({title:"Error",text:"Por favor seleccione una semana para generar el archivo.",icon:"error",confirmButtonText:"OK"});return}try{await I(m),p.titulo==="Multas Dominicales"&&(d==="pdf"?z(p.titulo,m):d==="excel"&&T(p.titulo,m))}catch{U.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}},O=p=>p?`${p.apellidoP} ${p.apellidoM} ${p.nombre}`:"Sin registro",z=p=>{const d=new da("landscape");d.setFontSize(12),d.text(`Reporte de ${p}`,14,20);const c=S.value.map(r=>{const a=r.numeroUnidad||"N/A",e=r.directivo?`${r.directivo.nombre_completo}`:"N/A",t=r.entradaSabado?D(r.entradaSabado.horaEntrada):"Sin registro",l=r.entradaSabado?O(r.entradaSabado.operador):"Sin registro",h=r.entradaSabado&&r.entradaSabado.ruta?r.entradaSabado.ruta.nombreRuta:"Sin registro",w=r.entradaLunesTemprana?D(r.entradaLunesTemprana.horaEntrada):"Sin registro",b=r.entradaLunesTemprana?O(r.entradaLunesTemprana.operador):"Sin registro",s=r.entradaLunesTemprana&&r.entradaLunesTemprana.ruta?r.entradaLunesTemprana.ruta.nombreRuta:"Sin registro";return[a,e,t,l,h,w,b,s]}),u=[["Numero Unidad","Socio/Prestador","Sábado (horaEntrada)","Operador (Sábado)","Ruta (Sábado)","Lunes (horaEntrada)","Operador (Lunes)","Ruta (Lunes)"]];d.autoTable({head:u,body:c,startY:24}),d.save(`${p}.pdf`)},T=(p,d)=>{const c=`${p}-Semana-${d}.xlsx`,u=[["Numero Unidad","Socio/Prestador","Sábado (horaEntrada)","Operador (Sábado)","Ruta (Sábado)","Lunes (horaEntrada)","Operador (Lunes)","Ruta (Lunes)","Justificado"]];console.log("en generarExcel:",S.value),S.value.forEach(e=>{const t=e.numeroUnidad||"N/A",l=e.directivo?`${e.directivo.nombre_completo}`:"N/A",h=e.entradaSabado?D(e.entradaSabado.horaEntrada):"Sin registro",w=e.entradaSabado?O(e.entradaSabado.operador):"Sin registro",b=e.entradaSabado&&e.entradaSabado.ruta?e.entradaSabado.ruta.nombreRuta:"Sin registro",s=e.entradaLunesTemprana?D(e.entradaLunesTemprana.horaEntrada):"Sin registro",$=e.entradaLunesTemprana?O(e.entradaLunesTemprana.operador):"Sin registro",_=e.entradaLunesTemprana&&e.entradaLunesTemprana.ruta?e.entradaLunesTemprana.ruta.nombreRuta:"Sin registro";u.push([t,l,h,w,b,s,$,_,""])});const r=N.book_new(),a=N.aoa_to_sheet(u);N.book_append_sheet(r,a,"Reporte_Multas"),sa(r,c)},j=Array.from({length:52},(p,d)=>d+1);return(p,d)=>(g(),f(y,null,E(P,c=>n("div",{key:c.titulo,class:"mb-4 bg-zinc-100 rounded-lg p-4"},[n("h3",Da,C(c.titulo),1),d[2]||(d[2]=n("div",{class:"bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"},null,-1)),n("div",Oa,[n("div",ka,[d[1]||(d[1]=n("h2",{class:"font-semibold text-l pt-0"},"Semana: ",-1)),L(n("select",{"onUpdate:modelValue":d[0]||(d[0]=u=>K(m)?m.value=u:m=u),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 p-2"},[(g(!0),f(y,null,E(v(j),u=>(g(),f("option",{key:u,value:u},"Semana "+C(u),9,La))),128))],512),[[F,v(m)]])])]),n("div",Fa,[(g(),f(y,null,E(B,u=>n("button",{key:u.tipo,class:A(u.clase),onClick:r=>V(c,u.tipo,o.unidad,{tipo:c.periodoSeleccionado,valor:c.periodoSeleccionado==="semana"?v(m):""})},[n("i",{class:A(u.icono+" mr-2 jump-icon")},null,2),na(" "+C(u.texto),1)],10,Na)),64))])])),64))}},Aa={class:"text-lg font-bold"},Ra={class:"flex flex-wrap gap-4 mb-3"},ja=["value"],Ka={class:"flex flex-wrap gap-4 mb-3"},Pa={class:"flex flex-wrap space-x-3 mb-2"},Ba=["onUpdate:modelValue"],Ia=["value"],Va=["value"],za=["value"],Ma={class:"flex flex-wrap space-x-3"},Ha=["onClick"],ta=2024,Ga={__name:"ReportesConcentrado",props:{message:{String,default:""},color:{String,default:""},unidad:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})},entrada:{type:Object,default:()=>({})},corte:{type:Object,default:()=>({})},castigo:{type:Object,default:()=>({})},ultimaCorrida:{type:Object,default:()=>({})},tipoUltimaCorrida:{type:Object,default:()=>({})}},setup(R){const o=W([]),S=W(!1),m=ra({unidad:null,operador:null}),P=async(r,a)=>{let e="";a.tipo==="semana"?e=route("reportes.concentradoSemana",{idUnidad:r,semana:a.valor}):a.tipo==="mes"||typeof a=="number"?e=route("reportes.concentradoMes",{idUnidad:r,mes:a.valor}):a.tipo==="anio"&&(e=route("reportes.concentradoAnio",{idUnidad:r,anio:a.valor}));try{const t=await ia.get(e);o.value=t.data,console.log("Datos consultados:",o.value)}catch{U.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}},B=async(r,a,e,t)=>{if(!e||!t.tipo||!t.valor){U.fire({title:"Error",text:"Por favor seleccione los parámetros para poder generar el archivo.",icon:"error",confirmButtonText:"OK"});return}let l={tipo:r.periodoSeleccionado,valor:""};r.periodoSeleccionado==="semana"?l.valor=d:r.periodoSeleccionado==="mes"?l.valor=c:r.periodoSeleccionado==="anio"&&(l.valor=u),S.value=!0;try{await P(e,l),r.titulo==="Concentrado"?a==="pdf"?D(r.titulo,l):a==="excel"?I(r.titulo,l):a==="imprimir"&&imprimirReporte(r.titulo,l):U.fire({title:`Generar el reporte "${r.titulo}" en ${a}`,text:"Lógica para generar este tipo de reporte aquí",icon:"info",confirmButtonText:"OK"})}catch(h){console.error(h),U.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}finally{S.value=!1}},D=(r,a)=>{const e=a.tipo==="semana"?`Semana ${a.valor}`:a.tipo==="mes"?T[a.valor-1]:a.valor,t=`${r}-${e}.pdf`,l=new da("landscape");l.setFontSize(12),l.text(`Reporte de ${r} - Período: ${e}`,14,20);const h=[{header:"Ruta",dataKey:"ruta"},{header:"Fecha",dataKey:"fecha"},{header:"Numero Unidad",dataKey:"numeroUnidad"},{header:"Socio/Prestador",dataKey:"socioPrestador"},{header:"Hora Entrada",dataKey:"horaEntrada"},{header:"Tipo Entrada",dataKey:"tipoEntrada"},{header:"Extremo",dataKey:"extremo"},{header:"Operador",dataKey:"operador"}],w=o.value.map(s=>{var $,_;return{ruta:(($=s.ruta)==null?void 0:$.nombreRuta)||"N/A",fecha:s.created_at?new Date(s.created_at).toLocaleDateString():"N/A",numeroUnidad:((_=s.unidad)==null?void 0:_.numeroUnidad)||"N/A",socioPrestador:s.directivo?`${s.directivo.nombre_completo}`:"N/A",horaEntrada:s.horaEntrada?s.horaEntrada.substring(0,5):"N/A",tipoEntrada:s.tipoEntrada||"Tarde",extremo:s.extremo||"N/A",operador:s.operador?`${s.operador.nombre_completo}`:"N/A"}});l.autoTable({head:[h.map(s=>s.header)],body:w.map(s=>h.map($=>s[$.dataKey])),startY:24,styles:{fontSize:10,cellPadding:4,halign:"center"}});const b=new Date().toLocaleDateString("es-ES",{day:"numeric",month:"long",year:"numeric"});l.setFontSize(10),l.text(`Fecha de creación: ${b}`,14,l.internal.pageSize.height-10),l.save(t)},I=(r,a)=>{let e;a.tipo==="semana"?e=`Semana ${a.valor}`:a.tipo==="mes"?e=T[a.valor-1]:(a.tipo,e=a.valor);const t=`${r}-${e}.xlsx`,l=[["Ruta","Fecha","Núm. Unidad","Domingo","Socio/Prestador","Operador","Hora Entrada","Tipo Entrada","Ext","Hora Corte","Hora Regreso","Causa De Corte","Hora Inicio Castigo","Hora Fin Castigo","Castigo","Observaciones","Hora Inicio UC","Hora Fin UC","Tipo UC"]];o.value.forEach(b=>{b.entradas.forEach(s=>{const $=s.ruta.nombreRuta||"N/A",_=s.created_at?new Date(s.created_at).toLocaleDateString():"N/A",la=b.unidad||"N/A",ca=s.directivo.nombre_completo||"N/A",ua=s.operador?`${s.operador.nombre_completo}`:"N/A",pa=s.horaEntrada?s.horaEntrada.substring(0,5):"N/A",ma=s.tipoEntrada||"",ga=s.extremo||"N/A";let X=" ";b.rolServicio.forEach(i=>{const k=new Date(i.created_at),oa=new Date(k);oa.setDate(k.getDate()+(7-k.getDay())),new Date(s.created_at).toDateString()===oa.toDateString()&&(X=i.trabajaDomingo)});let M="",H="",G="",Y="",J="",q="",Q="";b.cortes.forEach(i=>{new Date(i.created_at).toLocaleDateString()===_&&(M+=M?`, ${i.horaCorte?i.horaCorte.substring(0,5):""}`:i.horaCorte?i.horaCorte.substring(0,5):"",H+=H?`, ${i.horaRegreso?i.horaRegreso.substring(0,5):""}`:i.horaRegreso?i.horaRegreso.substring(0,5):"",G+=G?`, ${i.causa||""}`:i.causa||"")}),b.castigos.forEach(i=>{new Date(i.created_at).toLocaleDateString()===_&&(Y+=Y?`, ${i.horaInicio?i.horaInicio.substring(0,5):""}`:i.horaInicio?i.horaInicio.substring(0,5):"",J+=J?`, ${i.horaFin?i.horaFin.substring(0,5):""}`:i.horaFin?i.horaFin.substring(0,5):"",q+=q?`, ${i.castigo||""}`:i.castigo||"",Q+=Q?`, ${i.observaciones||""}`:i.observaciones||"")});let Z="",aa="",ea="";b.ultimaCorridas.forEach(i=>{new Date(i.created_at).toLocaleDateString()===_&&(Z=i.horaInicioUC?i.horaInicioUC.substring(0,5):"",aa=i.horaFinUC?i.horaFinUC.substring(0,5):"",ea=i.tipo_ultima_corrida.tipoUltimaCorrida||"")}),l.push([$,_,la,X,ca,ua,pa,ma,ga,M,H,G,Y,J,q,Q,Z,aa,ea])})});const h=N.book_new(),w=N.aoa_to_sheet(l);N.book_append_sheet(h,w,"Reporte_Concentrado"),sa(h,t)},V=[{titulo:"Concentrado",periodo:"semana",periodoSeleccionado:"semana"}],O=[{tipo:"pdf",texto:"Generar PDF",clase:"bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-pdf"},{tipo:"excel",texto:"Generar Excel",clase:"bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-excel"}],z=Array.from({length:52},(r,a)=>a+1),T=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],j=new Date().getFullYear(),p=Array.from({length:j-ta+1},(r,a)=>ta+a);let d=1,c=1,u=j;return(r,a)=>(g(),f(y,null,E(V,e=>n("div",{key:e.titulo,class:"mb-4 bg-zinc-100 rounded-lg p-4"},[n("h3",Aa,C(e.titulo),1),a[10]||(a[10]=n("div",{class:"bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"},null,-1)),n("div",Ra,[a[6]||(a[6]=n("h2",{class:"font-semibold text-l pt-0"},"Buscar por: ",-1)),n("div",null,[n("div",null,[L(n("select",{name:"unidad",id:"unidad","onUpdate:modelValue":a[0]||(a[0]=t=>m.unidad=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[a[4]||(a[4]=n("option",{disabled:"",select:"",value:""},"----- Unidad ----- ",-1)),a[5]||(a[5]=n("option",{value:"todas"},"Todas las unidades",-1)),(g(!0),f(y,null,E(R.unidad,t=>(g(),f("option",{key:t.idUnidad,value:t.idUnidad},C(t.numeroUnidad),9,ja))),128))],512),[[F,m.unidad]])])])]),n("div",Ka,[n("div",Pa,[a[8]||(a[8]=n("h2",{class:"font-semibold text-l pt-0"},"Periodo: ",-1)),L(n("select",{"onUpdate:modelValue":t=>e.periodoSeleccionado=t,class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},a[7]||(a[7]=[n("option",{value:"semana"},"Semanal",-1),n("option",{value:"mes"},"Mensual",-1),n("option",{value:"anio"},"Anual",-1)]),8,Ba),[[F,e.periodoSeleccionado]]),e.periodoSeleccionado==="semana"?L((g(),f("select",{key:0,"onUpdate:modelValue":a[1]||(a[1]=t=>K(d)?d.value=t:d=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(g(!0),f(y,null,E(v(z),(t,l)=>(g(),f("option",{key:l,value:t},"Semana "+C(t),9,Ia))),128))],512)),[[F,v(d)]]):e.periodoSeleccionado==="mes"?L((g(),f("select",{key:1,"onUpdate:modelValue":a[2]||(a[2]=t=>K(c)?c.value=t:c=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(g(),f(y,null,E(T,(t,l)=>n("option",{key:l,value:l+1},C(t),9,Va)),64))],512)),[[F,v(c)]]):e.periodoSeleccionado==="anio"?L((g(),f("select",{key:2,"onUpdate:modelValue":a[3]||(a[3]=t=>K(u)?u.value=t:u=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(g(!0),f(y,null,E(v(p),t=>(g(),f("option",{key:t,value:t},C(t),9,za))),128))],512)),[[F,v(u)]]):$a("",!0)])]),n("div",Ma,[(g(),f(y,null,E(O,t=>n("button",{key:t.tipo,class:A(t.clase),onClick:l=>B(e,t.tipo,m.unidad,{tipo:e.periodoSeleccionado,valor:e.periodoSeleccionado==="semana"?v(d):e.periodoSeleccionado==="mes"?v(c):e.periodoSeleccionado==="anio"?v(u):""})},[n("i",{class:A(t.icono+" mr-2 jump-icon")},null,2),na(" "+C(t.texto),1)],10,Ha)),64)),n("div",{class:A(["loading-overlay",{show:S.value}])},a[9]||(a[9]=[n("div",{class:"spinner"},null,-1)]),2)])])),64))}},Ya={class:"mt-1 bg-white p-4 shadow rounded-lg h-5/6"},te={__name:"Reportes",props:{message:{String,default:""},color:{String,default:""},usuario:{type:Object},unidad:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})},tipoUltimaCorrida:{type:Object,default:()=>({})},entrada:{type:Object,default:()=>({})},corte:{type:Object,default:()=>({})},ultimaCorrida:{type:Object,default:()=>({})},castigo:{type:Object,default:()=>({})}},setup(R){const o=R;return(S,m)=>(g(),wa(Ca,{title:"Reportes",usuario:o.usuario},{default:Ua(()=>[n("div",Ya,[m[0]||(m[0]=n("h2",{class:"font-bold text-center text-xl pt-0 mb-2"},"Reportes",-1)),m[1]||(m[1]=n("div",{class:"bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-2"},null,-1)),x(fa,{unidad:o.unidad,operador:o.operador,entrada:o.entrada},null,8,["unidad","operador","entrada"]),x(ba,{unidad:o.unidad,operador:o.operador,entrada:o.entrada},null,8,["unidad","operador","entrada"]),x(ha,{unidad:o.unidad,operador:o.operador,corte:o.corte},null,8,["unidad","operador","corte"]),x(xa,{unidad:o.unidad,operador:o.operador,corte:o.corte},null,8,["unidad","operador","corte"]),x(va,{unidad:o.unidad,operador:o.operador,corte:o.corte},null,8,["unidad","operador","corte"]),x(Sa,{unidad:o.unidad,operador:o.operador,tipoUltimaCorrida:o.tipoUltimaCorrida,ultimaCorrida:o.ultimaCorrida},null,8,["unidad","operador","tipoUltimaCorrida","ultimaCorrida"]),x(_a,{unidad:o.unidad,operador:o.operador,castigo:o.castigo},null,8,["unidad","operador","castigo"]),x(Ea,{unidad:o.unidad,operador:o.operador},null,8,["unidad","operador"]),x(ya,{unidad:o.unidad,operador:o.operador},null,8,["unidad","operador"]),x(Ta,{unidad:o.unidad,entrada:o.entrada,corte:o.corte,operador:o.operador},null,8,["unidad","entrada","corte","operador"]),x(Ga,{unidad:o.unidad,operador:o.operador,entrada:o.entrada,corte:o.corte,castigo:o.castigo,ultimaCorrida:o.ultimaCorrida,tipoUltimaCorrida:o.tipoUltimaCorrida},null,8,["unidad","operador","entrada","corte","castigo","ultimaCorrida","tipoUltimaCorrida"])])]),_:1},8,["usuario"]))}};export{te as default};