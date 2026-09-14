package com.siga.data;

import org.springframework.stereotype.Component;
import java.util.*;

@Component
public class ModuleRegistry {
    private final Map<String,ModuleDefinition> modules = new LinkedHashMap<>();
    public ModuleRegistry(){
        add("galpones","Galpones","🏠","galpones","galpones.ver", List.of(
                f("codigo","Código","text",true), f("nombre","Nombre","text",true), f("ubicacion","Ubicación","text",false),
                f("capacidad_maxima","Capacidad máxima","number",true), f("cantidad_actual","Cantidad actual","number",true), f("estado","Estado","select:activo|inactivo|mantenimiento",true)));
        add("agua","Registro de agua","💧","registros_agua","agua.ver", List.of(
                f("galpon_id","Galpón (ID)","number",true), f("fecha","Fecha","date",true), f("litros","Litros","decimal",true), f("observacion","Observación","textarea",false)));
        add("alimento","Registro de alimento","🌾","registros_alimento","alimento.ver", List.of(
                f("galpon_id","Galpón (ID)","number",true), f("fecha","Fecha","date",true), f("kilogramos","Kilogramos","decimal",true), f("observacion","Observación","textarea",false)));
        add("amoniaco","Registro de amoniaco","🧪","registros_amoniaco","amoniaco.ver", List.of(
                f("galpon_id","Galpón (ID)","number",true), f("fecha","Fecha","date",true), f("nivel_ppm","Nivel PPM","decimal",true), f("observacion","Observación","textarea",false)));
        add("mortalidad","Mortalidad","⚠️","mortalidad","mortalidad.ver", List.of(
                f("galpon_id","Galpón (ID)","number",true), f("fecha","Fecha","date",true), f("cantidad","Cantidad","integer",true), f("causa","Causa","text",false), f("observacion","Observación","textarea",false)));
        add("inventario","Inventario","📦","inventario","inventario.ver", List.of(
                f("tipo","Tipo","select:alimento|medicamento|insumo",true), f("nombre","Nombre","text",true), f("descripcion","Descripción","textarea",false),
                f("unidad_medida","Unidad","text",true), f("stock_actual","Stock actual","decimal",true), f("stock_minimo","Stock mínimo","decimal",true), f("fecha_vencimiento","Vencimiento","date",false), f("activo","Activo","checkbox",false)));
        add("movimientos","Movimientos de inventario","🔄","movimientos_inventario","inventario.ver", List.of(
                f("inventario_id","Inventario (ID)","number",true), f("tipo_movimiento","Tipo","select:entrada|salida",true), f("cantidad","Cantidad","integer",true), f("motivo","Motivo","text",false)));
        add("alertas","Alertas","🔔","alertas","alertas.ver", List.of(
                f("galpon_id","Galpón (ID)","number",false), f("tipo","Tipo","select:amoniaco|agua|alimento|sobrepoblacion|mortalidad",true),
                f("mensaje","Mensaje","textarea",true), f("nivel","Nivel","select:info|advertencia|critica",true), f("leida","Leída","checkbox",false)));
    }
    private ModuleDefinition.Field f(String n,String l,String t,boolean r){return new ModuleDefinition.Field(n,l,t,r);}
    private void add(String k,String t,String i,String table,String p,List<ModuleDefinition.Field> f){modules.put(k,new ModuleDefinition(k,t,i,table,p,f,""));}
    public Collection<ModuleDefinition> all(){return modules.values();}
    public ModuleDefinition get(String key){return Optional.ofNullable(modules.get(key)).orElseThrow();}
}
