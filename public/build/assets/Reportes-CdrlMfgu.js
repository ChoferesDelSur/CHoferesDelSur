import{_ as no}from"./ReporteOperadoresSinTrabajar-D4r6hUWA.js";import{S as V}from"./sweetalert2.esm.all-BGf-Fe8G.js";import{E as ro}from"./jspdf.plugin.autotable-BzyeIiG6.js";/* empty css                                                               */import{r as I,K as J,o as l,h as d,y as x,b as a,t as h,j as N,z as O,F as y,u as m,L as z,i as W,n as X,k as lo,I as co,a as mo,w as uo,d as q}from"./app-BIuP-68C.js";import{u as B,w as po}from"./xlsx-DjuO7_Ju.js";import{_ as vo}from"./RHLayout-Dkpq5UT_.js";const fo={class:"text-lg font-bold"},go={class:"flex flex-wrap gap-4 mb-3"},bo=["value"],xo={class:"flex flex-wrap gap-4 mb-3"},ho={class:"flex flex-wrap space-x-3 mb-2"},yo=["onUpdate:modelValue"],_o=["value"],Mo=["value"],Ao=["value"],wo=["value"],ko={class:"flex flex-wrap space-x-3"},Do=["onClick"],H=2024,So={__name:"ReportesMovimientos",props:{message:{String,default:""},color:{String,default:""},unidad:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})},directivo:{type:Object,default:()=>({})},movimiento:{type:Object,default:()=>({})},tipoMovimiento:{type:Object,default:()=>({})}},setup(C){const c=I([]),R=J({directivo:null,movimiento:null}),_=async(i,o)=>{let e="",t=o.valor.value?o.valor.value:o.valor,n=t;if(o.tipo==="dia"&&t.includes("/")){const[u,D,S]=t.split("/");n=`${u}-${D}-${S}`}o.tipo==="semana"?e=route("reportes.MovSemana",{idDirectivo:i,semana:t}):o.tipo==="mes"||typeof o=="number"?e=route("reportes.MovMes",{idDirectivo:i,mes:t}):o.tipo==="anio"?e=route("reportes.MovAnio",{idDirectivo:i,anio:t}):o.tipo==="dia"&&(e=route("reportes.MovDia",{idDirectivo:i,dia:n}));try{const u=await co.get(e);c.value=u.data}catch{V.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}},Q=async(i,o,e,t)=>{if(!e||!t.tipo||!t.valor){V.fire({title:"Error",text:"Por favor seleccione los parámetros para poder generar el archivo.",icon:"error",confirmButtonText:"OK"});return}let n={tipo:i.periodoSeleccionado,valor:""};i.periodoSeleccionado==="semana"?n.valor=A:i.periodoSeleccionado==="mes"?n.valor=w:i.periodoSeleccionado==="anio"?n.valor=k:i.periodoSeleccionado==="dia"&&(n.valor=M);try{await _(e,n),i.titulo==="Movimientos"?o==="pdf"?Z(i.titulo,n):o==="excel"?oo(i.titulo,n):o==="imprimir"&&imprimirReporte(i.titulo,n):V.fire({title:`Generar el reporte "${i.titulo}" en ${o}`,text:"Lógica para generar este tipo de reporte aquí",icon:"info",confirmButtonText:"OK"})}catch(u){console.error("Error al obtener datos:",u),V.fire({title:"Error",text:"No se pudieron obtener los datos",icon:"error",confirmButtonText:"OK"})}},Z=(i,o)=>{const e=new ro({orientation:"landscape"});let t;o.tipo==="semana"?t=`Semana ${o.valor}`:o.tipo==="mes"?t=P[o.valor-1]:o.tipo==="anio"?t=o.valor:o.tipo==="dia"?t=typeof o.valor=="string"?o.valor:o.valor._value||"Fecha inválida":t=o.valor,e.setFontSize(12),e.text(`Reporte de ${i} - Período: ${t}`,14,20);const n=c.value.filter(r=>r.estado.idEstado===1),u=c.value.filter(r=>r.estado.idEstado===2);e.setFontSize(12),e.text("Movimientos de Alta",14,30);const D=["Fecha","Movimiento","Operador","Socio/Prestador","Tipo De Movimiento"],S=n.map(r=>{var $,T;const b=r.fechaMovimiento||"N/A",v=r.estado?r.estado.estado:"N/A",f=r.operador?`${r.operador.nombre_completo}`:"N/A",K=(($=r.directivo)==null?void 0:$.nombre_completo)||"N/A",L=((T=r.tipo_movimiento)==null?void 0:T.tipoMovimiento)||"N/A";return[b,v,f,K,L]});e.autoTable({head:[D],body:S,startY:34,margin:{top:30}}),e.setFontSize(12),e.text("Movimientos de Baja",14,e.autoTable.previous.finalY+10);const j=["Fecha","Movimiento","Operador","Socio/Prestador","Tipo De Movimiento"],Y=u.map(r=>{var $,T;const b=r.fechaMovimiento||"N/A",v=r.estado?r.estado.estado:"N/A",f=r.operador?`${r.operador.nombre_completo}`:"N/A",K=(($=r.directivo)==null?void 0:$.nombre_completo)||"N/A",L=((T=r.tipo_movimiento)==null?void 0:T.tipoMovimiento)||"N/A";return[b,v,f,K,L]});e.autoTable({head:[j],body:Y,startY:e.autoTable.previous.finalY+14,margin:{top:30}});const p=e.autoTable.previous.finalY+25,s=14,g=150;e.setFontSize(12),e.text("Elaboró:",s,p),e.line(s+20,p+2,s+130,p+2),e.text("Recibió:",g,p),e.line(g+20,p+2,g+100,p+2),e.text("Jefa de Departamento de Recursos Humanos",s+30,p+10),e.text("Departamento de Servicio",g+30,p+10);const F=new Date().toLocaleDateString("es-ES",{day:"numeric",month:"long",year:"numeric"});e.setFontSize(10),e.text(`Fecha de creación: ${F}`,14,e.internal.pageSize.height-10);const E=`${i}-${t}.pdf`;e.save(E)},oo=(i,o)=>{let e;o.tipo==="semana"?e=`Semana ${o.valor}`:o.tipo==="mes"?e=P[o.valor-1]:o.tipo==="anio"?e=o.valor:o.tipo==="dia"?e=typeof o.valor=="string"?o.valor:o.valor._value||"Fecha inválida":e=o.valor;const t=`${i}-${e}.xlsx`,n=c.value.filter(s=>s.estado.idEstado===1),u=c.value.filter(s=>s.estado.idEstado===2),D=[["Fecha","Movimiento","Operador","Directivo","Tipo De Movimiento"]];n.forEach(s=>{var v,f;const g=s.fechaMovimiento||"N/A",F=s.estado?s.estado.estado:"N/A",E=s.operador?`${s.operador.nombre_completo}`:"N/A",r=((v=s.directivo)==null?void 0:v.nombre_completo)||"N/A",b=((f=s.tipo_movimiento)==null?void 0:f.tipoMovimiento)||"N/A";D.push([g,F,E,r,b])});const S=[["Fecha","Movimiento","Operador","Directivo","Tipo De Movimiento"]];u.forEach(s=>{var v,f;const g=s.fechaMovimiento||"N/A",F=s.estado?s.estado.estado:"N/A",E=s.operador?`${s.operador.nombre_completo}`:"N/A",r=((v=s.directivo)==null?void 0:v.nombre_completo)||"N/A",b=((f=s.tipo_movimiento)==null?void 0:f.tipoMovimiento)||"N/A";S.push([g,F,E,r,b])});const j=B.book_new(),Y=B.aoa_to_sheet(D),p=B.aoa_to_sheet(S);B.book_append_sheet(j,Y,"Movimientos de Alta"),B.book_append_sheet(j,p,"Movimientos de Baja"),po(j,t)},eo=[{titulo:"Movimientos",periodo:"dia",periodoSeleccionado:"dia"}],to=[{tipo:"pdf",texto:"Generar PDF",clase:"bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-pdf"},{tipo:"excel",texto:"Generar Excel",clase:"bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded",icono:"fa-solid fa-file-excel"}],io=Array.from({length:52},(i,o)=>o+1),P=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],G=new Date().getFullYear(),ao=Array.from({length:G-H+1},(i,o)=>H+o);let U=i=>{const o={day:"2-digit",month:"2-digit",year:"numeric"};return new Intl.DateTimeFormat("es-ES",o).format(i)},M=I(U(new Date)),A=1,w=1,k=G;Array.from({length:7},(i,o)=>{const e=new Date,t=e.getDay()||7;return e.setDate(e.getDate()-t+1+o),U(e)});const so=Array.from({length:7},(i,o)=>{const e=new Date,t=e.getDay()||7;return e.setDate(e.getDate()-t+1+o),U(e)});return J({periodoSeleccionado:"",semana:null,mes:null,anio:null,dia:null}),(i,o)=>(l(),d(y,null,x(eo,e=>a("div",{key:e.titulo,class:"mb-4 bg-zinc-100 rounded-lg p-4"},[a("h3",fo,h(e.titulo),1),o[10]||(o[10]=a("div",{class:"bg-gradient-to-r from-cyan-500 to-cyan-500 h-px mb-2"},null,-1)),a("div",go,[o[7]||(o[7]=a("h2",{class:"font-semibold text-l pt-0"},"Buscar por: ",-1)),a("div",null,[a("div",null,[N(a("select",{name:"directivo",id:"directivo","onUpdate:modelValue":o[0]||(o[0]=t=>R.directivo=t),class:"block w-72 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[o[5]||(o[5]=a("option",{disabled:"",select:"",value:""},"--------------- Socio/Prestador --------------- ",-1)),o[6]||(o[6]=a("option",{value:"todas"},"Todos los socios y prestadores",-1)),(l(!0),d(y,null,x(C.directivo,t=>(l(),d("option",{key:t.idDirectivo,value:t.idDirectivo},h(t.nombre_completo),9,bo))),128))],512),[[O,R.directivo]])])])]),a("div",xo,[a("div",ho,[o[9]||(o[9]=a("h2",{class:"font-semibold text-l pt-0"},"Periodo: ",-1)),N(a("select",{"onUpdate:modelValue":t=>e.periodoSeleccionado=t,class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},o[8]||(o[8]=[a("option",{value:"dia"},"Día",-1),a("option",{value:"semana"},"Semanal",-1),a("option",{value:"mes"},"Mensual",-1),a("option",{value:"anio"},"Anual",-1)]),8,yo),[[O,e.periodoSeleccionado]]),e.periodoSeleccionado==="dia"?N((l(),d("select",{key:0,"onUpdate:modelValue":o[1]||(o[1]=t=>z(M)?M.value=t:M=t),class:"block w-28 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(l(!0),d(y,null,x(m(so),(t,n)=>(l(),d("option",{key:n,value:t},h(t),9,_o))),128))],512)),[[O,m(M)]]):W("",!0),e.periodoSeleccionado==="semana"?N((l(),d("select",{key:1,"onUpdate:modelValue":o[2]||(o[2]=t=>z(A)?A.value=t:A=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(l(!0),d(y,null,x(m(io),(t,n)=>(l(),d("option",{key:n,value:t},"Semana "+h(t),9,Mo))),128))],512)),[[O,m(A)]]):e.periodoSeleccionado==="mes"?N((l(),d("select",{key:2,"onUpdate:modelValue":o[3]||(o[3]=t=>z(w)?w.value=t:w=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(l(),d(y,null,x(P,(t,n)=>a("option",{key:n,value:n+1},h(t),9,Ao)),64))],512)),[[O,m(w)]]):e.periodoSeleccionado==="anio"?N((l(),d("select",{key:3,"onUpdate:modelValue":o[4]||(o[4]=t=>z(k)?k.value=t:k=t),class:"block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[(l(!0),d(y,null,x(m(ao),t=>(l(),d("option",{key:t,value:t},h(t),9,wo))),128))],512)),[[O,m(k)]]):W("",!0)])]),a("div",ko,[(l(),d(y,null,x(to,t=>a("button",{key:t.tipo,class:X(t.clase),onClick:n=>Q(e,t.tipo,R.directivo,{tipo:e.periodoSeleccionado,valor:e.periodoSeleccionado==="dia"?m(M):e.periodoSeleccionado==="semana"?m(A):e.periodoSeleccionado==="mes"?m(w):e.periodoSeleccionado==="anio"?m(k):""})},[a("i",{class:X(t.icono+" mr-2 jump-icon")},null,2),lo(" "+h(t.texto),1)],10,Do)),64))])])),64))}},No={class:"mt-1 bg-white p-4 shadow rounded-lg h-5/6"},Ro={__name:"Reportes",props:{message:{String,default:""},color:{String,default:""},usuario:{type:Object},unidad:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})},tipoUltimaCorrida:{type:Object,default:()=>({})},movimiento:{type:Object,default:()=>({})},directivo:{type:Object,default:()=>({})},tipoMovimiento:{type:Object,default:()=>({})}},setup(C){const c=C;return(R,_)=>(l(),mo(vo,{title:"Reportes",usuario:c.usuario},{default:uo(()=>[a("div",No,[_[0]||(_[0]=a("h2",{class:"font-bold text-center text-xl pt-0 mb-2"},"Reportes",-1)),_[1]||(_[1]=a("div",{class:"bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-2"},null,-1)),q(So,{unidad:c.unidad,operador:c.operador,directivo:c.directivo,movimiento:c.movimiento,tipoMovimiento:c.tipoMovimiento},null,8,["unidad","operador","directivo","movimiento","tipoMovimiento"]),q(no,{unidad:c.unidad,operador:c.operador},null,8,["unidad","operador"])])]),_:1},8,["usuario"]))}};export{Ro as default};