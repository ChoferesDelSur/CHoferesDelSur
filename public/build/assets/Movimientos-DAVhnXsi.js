import{e as z,r as h,c as A,x as H,o as u,a as L,w as N,b as e,g as R,t as y,k as x,j as w,u as v,v as $,h as f,i as _,F as S,y as E,z as O,C,D as P,f as W,d as F}from"./app-DKY7pHet.js";import"./buttons.print-cnAWLXhX.js";import{S as I}from"./sweetalert2.esm.all-BGf-Fe8G.js";import{_ as V}from"./Mensaje-DAM774KY.js";import{_ as q}from"./RHLayout-CJCzfuSD.js";import{_ as G}from"./Modal-BynIwB2A.js";/* empty css                                                */import"./jspdf.plugin.autotable-CneeF7WA.js";const X={class:"mt-2 bg-white p-4 shadow rounded-lg"},J={class:"border-b border-gray-900/10 pb-12"},K={class:"text-base font-semibold leading-7 text-gray-900"},Q={class:"flex flex-wrap -mx-4"},Y={class:"sm:col-span-2"},Z={class:"sm:col-span-1",hidden:""},ee={class:"mt-2"},oe=["id"],te={class:"sm:col-span-2 px-4"},ie={class:"mt-2"},se=["id"],re={key:0,class:"text-red-500 text-xs mt-1"},ae={class:"sm:col-span-2 px-4"},ne={class:"mt-2"},de=["id"],le=["value"],me={key:0,class:"text-red-500 text-xs mt-1"},ce={class:"sm:col-span-2 px-4"},pe={class:"mt-2"},ue=["id"],ve=["value"],ge={key:0,class:"text-red-500 text-xs mt-1"},fe={class:"sm:col-span-2 px-4"},be={class:"mt-2"},xe=["id"],ye=["value"],he={key:0,class:"text-red-500 text-xs mt-1"},Me={class:"sm:col-span-2 px-4"},we={class:"mt-2"},_e=["id"],ke=["value"],Se={key:0,class:"text-red-500 text-xs mt-1"},Ee={class:"mt-6 flex items-center justify-end gap-x-6"},Oe=["id"],je=["disabled"],Be={__name:"FormularioMovimiento",props:{show:{type:Boolean,default:!1,hora:String},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0},directivo:{type:Object,default:()=>({})},operador:{type:Object,default:()=>({})},operadoresAlta:{type:Object,default:()=>({})},operadoresBaja:{type:Object,default:()=>({})},estado:{type:Object,default:()=>({})},movimiento:{type:Object,default:()=>({})},tipoMovimiento:{type:Object,default:()=>({})},title:{type:String},modal:{type:String},op:{type:String}},emits:["close"],setup(p,{emit:c}){const g=p,j=c,s=z({idMovimiento:g.movimiento.idMovimiento,fechaMovimiento:g.movimiento.fechaMovimiento,estado:g.movimiento.idEstado,operador:g.movimiento.idOperador,directivo:g.movimiento.idDirectivo,tipoMovimiento:g.movimiento.idTipoMovimiento}),b=h(g.estado.filter(a=>a.idEstado===1||a.idEstado===2)),B=A(()=>s.estado?g.tipoMovimiento.filter(a=>a.idEstado===s.estado):[]),k=A(()=>{let a=[];return s.estado===1?a=g.operadoresBaja:s.estado===2&&(a=g.operadoresAlta),a.sort((o,i)=>o.nombre_completo.localeCompare(i.nombre_completo))}),D=A(()=>g.directivo.sort((a,o)=>a.nombre_completo.localeCompare(o.nombre_completo))),M=h(""),r=h(""),t=h(""),n=h(""),l=h("");H(()=>s.operador,a=>{const o=k.value.find(i=>i.idOperador===a);o&&(s.directivo=o.idDirectivo)});const d=async()=>{j("close"),s.reset()},m=a=>a!=null,T=a=>!(a==null||((typeof a=="string"||a instanceof String)&&(a=new Date(a)),!(a instanceof Date))||isNaN(a.getTime())),U=async()=>{M.value=T(s.fechaMovimiento)?"":"Fecha de movimiento no válido",r.value=m(s.estado)?"":"Seleccione el movimiento a realizar",t.value=m(s.operador)?"":"Seleccione al operador",n.value=m(s.directivo)?"":"Seleccione al directivo",l.value=m(s.tipoMovimiento)?"":"Seleccione el tipo de movimiento",!(M.value||r.value||t.value||n.value||l.value)&&s.post(route("rh.addMovimiento"),{onSuccess:()=>{d(),M.value="",r.value="",t.value="",n.value="",l.value=""}})};return(a,o)=>(u(),L(G,{show:p.show,"max-width":p.maxWidth,closeable:p.closeable,onClose:d},{default:N(()=>[e("div",X,[e("form",{onSubmit:R(U,["prevent"])},[e("div",J,[e("h2",K,y(p.title),1),o[16]||(o[16]=e("p",{class:"mt-1 text-sm leading-6 text-gray-600 mb-4"},[x("Rellene el formulario para poder registrar un nuevo movimiento. Los campos con "),e("span",{class:"text-red-500"},"*"),x(" son obligatorios. ")],-1)),e("div",Q,[e("div",Y,[e("div",Z,[o[6]||(o[6]=e("label",{for:"idMovimiento",class:"block text-sm font-medium leading-6 text-gray-900"},"idMovimiento",-1)),e("div",ee,[w(e("input",{type:"number",name:"idMovmiento","onUpdate:modelValue":o[0]||(o[0]=i=>v(s).idMovimiento=i),placeholder:"Ingrese id del movimiento",id:"idMovimiento"+p.op,class:"block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},null,8,oe),[[$,v(s).idMovimiento]])])])]),e("div",te,[o[7]||(o[7]=e("label",{for:"fechaMovimiento",class:"block text-sm font-medium leading-6 text-gray-900"},[x("Fecha de Movimiento "),e("span",{class:"text-red-500"},"*")],-1)),e("div",ie,[w(e("input",{type:"date",name:"fechaMovimiento",id:"fechaMovimiento"+p.op,"onUpdate:modelValue":o[1]||(o[1]=i=>v(s).fechaMovimiento=i),placeholder:"Ingrese la fecha de movimiento",class:"block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},null,8,se),[[$,v(s).fechaMovimiento]])]),M.value!=""?(u(),f("div",re,y(M.value),1)):_("",!0)]),e("div",ae,[o[9]||(o[9]=e("label",{for:"estado",class:"block text-sm font-medium leading-6 text-gray-900"},[x("Movimiento a realizar "),e("span",{class:"text-red-500"},"*")],-1)),e("div",ne,[w(e("select",{name:"tipoDirectivo",id:"tipoDirectivo"+p.op,"onUpdate:modelValue":o[2]||(o[2]=i=>v(s).estado=i),placeholder:"Seleccione el tipo de directivo",class:"block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[o[8]||(o[8]=e("option",{value:"",disabled:"",selected:""},"Seleccione el tipo de movimiento",-1)),(u(!0),f(S,null,E(b.value,i=>(u(),f("option",{key:i.idEstado,value:i.idEstado},y(i.estado),9,le))),128))],8,de),[[O,v(s).estado]])]),r.value!=""?(u(),f("div",me,y(r.value),1)):_("",!0)]),e("div",ce,[o[11]||(o[11]=e("label",{for:"operador",class:"block text-sm font-medium leading-6 text-gray-900"},[x("Operador "),e("span",{class:"text-red-500"},"*")],-1)),e("div",pe,[w(e("select",{name:"operador",id:"operador"+p.op,"onUpdate:modelValue":o[3]||(o[3]=i=>v(s).operador=i),placeholder:"Seleccione al operador",class:"block w-54 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[o[10]||(o[10]=e("option",{value:"",disabled:"",selected:""},"Seleccione al operador",-1)),(u(!0),f(S,null,E(k.value,i=>(u(),f("option",{key:i.idOperador,value:i.idOperador},y(i.nombre_completo),9,ve))),128))],8,ue),[[O,v(s).operador]])]),t.value!=""?(u(),f("div",ge,y(t.value),1)):_("",!0)]),e("div",fe,[o[13]||(o[13]=e("label",{for:"directivo",class:"block text-sm font-medium leading-6 text-gray-900"},[x("Socio / Prestador "),e("span",{class:"text-red-500"},"*")],-1)),e("div",be,[w(e("select",{name:"directivo",id:"directivo"+p.op,"onUpdate:modelValue":o[4]||(o[4]=i=>v(s).directivo=i),placeholder:"Seleccione al Socio/Prestador",class:"block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[o[12]||(o[12]=e("option",{value:"",disabled:"",selected:""},"Seleccione al Socio/Prestador",-1)),(u(!0),f(S,null,E(D.value,i=>(u(),f("option",{key:i.idDirectivo,value:i.idDirectivo},y(i.nombre_completo),9,ye))),128))],8,xe),[[O,v(s).directivo]])]),n.value!=""?(u(),f("div",he,y(n.value),1)):_("",!0)]),e("div",Me,[o[15]||(o[15]=e("label",{for:"tipoMovimiento",class:"block text-sm font-medium leading-6 text-gray-900"},[x("Tipo de Movimiento "),e("span",{class:"text-red-500"},"*")],-1)),e("div",we,[w(e("select",{name:"tipoMovimiento",id:"tipoMovimiento"+p.op,"onUpdate:modelValue":o[5]||(o[5]=i=>v(s).tipoMovimiento=i),placeholder:"Seleccione el tipo de movimiento",class:"block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},[o[14]||(o[14]=e("option",{value:"",disabled:"",selected:""},"Seleccione el tipo de movimiento",-1)),(u(!0),f(S,null,E(B.value,i=>(u(),f("option",{key:i.idTipoMovimiento,value:i.idTipoMovimiento},y(i.tipoMovimiento),9,ke))),128))],8,_e),[[O,v(s).tipoMovimiento]])]),l.value!=""?(u(),f("div",Se,y(l.value),1)):_("",!0)])])]),e("div",Ee,[e("button",{type:"button",id:"cerrar"+p.op,class:"text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded","data-bs.dismiss":"modal",onClick:d},o[17]||(o[17]=[e("i",{class:"fa-solid fa-ban"},null,-1),x(" Cancelar")]),8,Oe),e("button",{type:"submit",class:"bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded",disabled:v(s).processing},o[18]||(o[18]=[e("i",{class:"fa-solid fa-floppy-disk mr-2"},null,-1),x("Guardar")]),8,je)])],32)])]),_:1},8,["show","max-width","closeable"]))}},De={class:"mt-0 bg-white p-4 shadow rounded-lg h-5/6"},Te={class:"py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0"},Ae={class:"overflow-x-auto"},Ce="xl",Fe=!0,Re={__name:"Movimientos",props:{message:{String,default:""},color:{String,default:""},operador:{type:Object},operadoresAlta:{type:Object},operadoresBaja:{type:Object},estado:{type:Object},directivo:{type:Object},movimiento:{type:Object},usuario:{type:Object},tipoMovimiento:{type:Object}},setup(p){C.use(P),C.use(P);const c=p,g=[{extend:"copyHtml5",text:'<i class="fa-solid fa-copy"></i> Copiar',className:"bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",exportOptions:{columns:[0,2,3,4,5,6]},button:!0},{title:"Movimientos registrados",extend:"excelHtml5",text:'<i class="fa-solid fa-file-excel"></i> Excel',className:"bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",exportOptions:{columns:[2,3,4,5,6,7,8]}},{title:"Movimientos registrados",extend:"pdfHtml5",text:'<i class="fa-solid fa-file-pdf"></i> PDF',className:"bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",orientation:"landscape",exportOptions:{columns:[2,3,4,5,6,7,8]}},{title:"Operadores registrados",extend:"print",text:'<i class="fa-solid fa-print"></i> Imprimir',className:"bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",exportOptions:{columns:[1,2,3,4,5,6]}}],j=[{data:null,render:function(r,t,n,l){return`<input type="checkbox" class="movimiento-checkboxes" data-id="${n.idMovimiento}" ">`}},{data:null,render:function(r,t,n,l){return l.row+1}},{data:"fechaMovimiento"},{data:"idEstado",render:function(r,t,n,l){const d=c.estado.find(m=>m.idEstado===r);return d?d.estado:""}},{data:"idOperador",render:function(r,t,n,l){const d=c.operador.find(m=>m.idOperador===r);return d?d.nombre_completo:""}},{data:"idDirectivo",render:function(r,t,n,l){const d=c.directivo.find(m=>m.idDirectivo===r);return d?d.nombre_completo:""}},{data:"idTipoMovimiento",render:function(r,t,n,l){const d=c.tipoMovimiento.find(m=>m.idTipoMovimiento===r);return d?d.tipoMovimiento:""}}],s=h(!1);h(!1);const b=h([]),B=z({_method:"DELETE"}),k=()=>{s.value=!1},D=r=>{b.value.includes(r)?b.value=b.value.filter(n=>n!==r):b.value.push(r);const t=document.getElementById("eliminarABtn");b.value.length>0?t.removeAttribute("disabled"):t.setAttribute("disabled","")};W(()=>{document.getElementById("movimientosTablaId").addEventListener("click",r=>{const t=r.target;if(t.classList.contains("movimiento-checkboxes")){const n=parseInt(t.getAttribute("data-id"));if(c.movimiento){const l=c.movimiento.find(d=>d.idMovimiento===n);l?D(l):console.log("No se tiene movimiento")}}})});const M=()=>{const r=I.mixin({buttonsStyling:!0}),t=b.value.map(n=>{const l=c.operador.find(m=>m.idOperador===n.idOperador),d=c.estado.find(m=>m.idEstado===n.idEstado);return`${l?l.nombre_completo:"Desconocido"} ~ ${d?d.estado:"Desconocido"}`}).join("<br>");r.fire({title:"¿Estas seguro que deseas eliminar el movimiento seleccionado?",html:`Movimiento seleccionado:<br>${t}`,icon:"warning",showCancelButton:!0,confirmButtonText:'<i class="fa-solid fa-check"></i> Confirmar',cancelButtonText:'<i class="fa-solid fa-ban"></i> Cancelar'}).then(async n=>{if(n.isConfirmed)try{const d=b.value.map(T=>T.idMovimiento).join(",");await B.post(route("rh.eliminarMovimiento",d)),b.value=[];const m=document.getElementById("eliminarABtn");b.value.length>0?m.removeAttribute("disabled"):m.setAttribute("disabled",""),I.fire({title:"Movimiento(s) eliminado(s) correctamente",icon:"success"}),window.flash={message:"Movimiento(s) eliminado(s) correctamente",color:"green"}}catch(l){console.log("Error al eliminar varios movimientos: "+l),I.fire({title:"Error",text:"Hubo un error al eliminar el movimiento. Por favor, inténtalo de nuevo más tarde.",icon:"error"})}})};return(r,t)=>(u(),L(q,{title:"Movimientos",usuario:c.usuario},{default:N(()=>[e("div",De,[t[4]||(t[4]=e("h2",{class:"font-bold text-center text-xl pt-0"},"Movimientos",-1)),t[5]||(t[5]=e("div",{class:"my-1"},null,-1)),t[6]||(t[6]=e("div",{class:"bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"},null,-1)),F(V),e("div",Te,[e("button",{class:"bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded",onClick:t[0]||(t[0]=n=>s.value=!0),"data-bs-toggle":"modal","data-bs-target":"#modalCreate"},t[1]||(t[1]=[e("i",{class:"fa fa-plus mr-2"},null,-1),x("Realizar Movimiento ")])),e("button",{id:"eliminarABtn",disabled:"",class:"bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded",onClick:M},t[2]||(t[2]=[e("i",{class:"fa fa-trash mr-2"},null,-1),x(" Eliminar Movimiento ")]))]),e("div",Ae,[F(v(C),{class:"w-full table-auto text-sm display nowrap stripe compact cell-border order-column",id:"movimientosTablaId",name:"movimientosTablaId",columns:j,data:p.movimiento,options:{responsive:!1,autoWidth:!1,dom:"Bftrip",language:{search:"Buscar",zeroRecords:"No hay registros para mostrar",info:"Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",infoEmpty:"Mostrando registros del 0 al 0 de un total de 0 registros",infoFiltered:"(filtrado de un total de _MAX_ registros)"},buttons:[g],paging:!1}},{default:N(()=>t[3]||(t[3]=[e("thead",null,[e("tr",{class:"text-sm leading-normal"},[e("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}),e("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," ID "),e("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," Fecha de Movimiento "),e("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," Movimiento "),e("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," Operador "),e("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," Socio / Prestador "),e("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light"}," Tipo de Movimiento ")])],-1)])),_:1},8,["data","options"])])]),F(Be,{show:s.value,"max-width":Ce,closeable:Fe,onClose:k,title:"Añadir Movimento",modal:"modalCreate",operador:c.operador,estado:c.estado,directivo:c.directivo,movimiento:c.movimiento,tipoMovimiento:c.tipoMovimiento,operadoresAlta:c.operadoresAlta,operadoresBaja:c.operadoresBaja},null,8,["show","operador","estado","directivo","movimiento","tipoMovimiento","operadoresAlta","operadoresBaja"])]),_:1},8,["usuario"]))}};export{Re as default};