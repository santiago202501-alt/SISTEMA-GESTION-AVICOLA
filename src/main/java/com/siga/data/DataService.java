package com.siga.data;

import com.siga.security.SigaUser;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.sql.Date;
import java.util.*;

@Service
public class DataService {
    private final JdbcTemplate jdbc;
    private final ModuleRegistry registry;
    public DataService(JdbcTemplate jdbc, ModuleRegistry registry){this.jdbc=jdbc;this.registry=registry;}

    public List<Map<String,Object>> list(ModuleDefinition m, SigaUser u){
        String sql="SELECT * FROM "+m.table()+" WHERE 1=1";
        List<Object> args=new ArrayList<>();
        if(!isAdmin(u)){sql += " AND cliente_id=?"; args.add(u.getClienteId());}
        sql += " ORDER BY id DESC";
        return jdbc.queryForList(sql,args.toArray());
    }
    public Map<String,Object> one(ModuleDefinition m,long id,SigaUser u){
        String sql="SELECT * FROM "+m.table()+" WHERE id=?"; List<Object> args=new ArrayList<>(List.of(id));
        if(!isAdmin(u)){sql+=" AND cliente_id=?";args.add(u.getClienteId());}
        return jdbc.queryForMap(sql,args.toArray());
    }
    public void save(ModuleDefinition m,long id,Map<String,String> form,SigaUser u){
        boolean insert=id==0; List<Object> vals=new ArrayList<>();
        if(insert){
            validateReferences(m, form, u);
            List<String> names=new ArrayList<>(); List<String> qs=new ArrayList<>();
            for(var f:m.fields()){names.add(f.name());qs.add("?");vals.add(value(form.get(f.name()),f.type()));}
            names.add("cliente_id"); qs.add("?"); vals.add(u.getClienteId());
            if (m.table().equals("galpones")) { names.add("responsable_id"); qs.add("?"); vals.add(u.getId()); }
            else if (m.table().equals("registros_agua") || m.table().equals("registros_alimento") || m.table().equals("registros_amoniaco") || m.table().equals("mortalidad") || m.table().equals("movimientos_inventario")) { names.add("usuario_id"); qs.add("?"); vals.add(u.getId()); }
            String sql="INSERT INTO "+m.table()+" ("+String.join(",",names)+") VALUES ("+String.join(",",qs)+")";
            jdbc.update(sql, vals.toArray());
        } else {
            validateReferences(m, form, u);
            List<String> sets=new ArrayList<>();
            for(var f:m.fields()){sets.add(f.name()+"=?");vals.add(value(form.get(f.name()),f.type()));}
            vals.add(id); String where="id=?";
            if(!isAdmin(u)){where+=" AND cliente_id=?";vals.add(u.getClienteId());}
            jdbc.update("UPDATE "+m.table()+" SET "+String.join(",",sets)+" WHERE "+where, vals.toArray());
        }
    }
    public void delete(ModuleDefinition m,long id,SigaUser u){
        if(!isAdmin(u) && !u.getAuthorities().stream().anyMatch(a->a.getAuthority().equals(m.permission().replace(".ver",".eliminar")))) return;
        String sql="DELETE FROM "+m.table()+" WHERE id=?"; List<Object> args=new ArrayList<>(List.of(id));
        if(!isAdmin(u)){sql+=" AND cliente_id=?";args.add(u.getClienteId());}
        jdbc.update(sql,args.toArray());
    }
    private boolean isAdmin(SigaUser u){return u.getAuthorities().stream().anyMatch(a->a.getAuthority().equals("ROLE_ADMINISTRADOR"));}
    private Object value(String s,String type){
        if("checkbox".equals(type)) return "on".equalsIgnoreCase(s)||"1".equals(s)||"true".equalsIgnoreCase(s)?1:0;
        if(s==null||s.isBlank()) return null;
        try { if("integer".equals(type)) return Long.valueOf(s); if("decimal".equals(type)) return new BigDecimal(s); if("date".equals(type)) return Date.valueOf(s); } catch(Exception ignored) { return null; }
        return s;
    }
    private void validateReferences(ModuleDefinition m, Map<String,String> form, SigaUser u){
        if(isAdmin(u)) return;
        for(String key: List.of("galpon_id","inventario_id")){
            if(form.containsKey(key) && form.get(key)!=null && !form.get(key).isBlank()){
                long id=Long.parseLong(form.get(key));
                String table=key.equals("galpon_id")?"galpones":"inventario";
                Integer ok=jdbc.queryForObject("SELECT COUNT(*) FROM "+table+" WHERE id=? AND cliente_id=?",Integer.class,id,u.getClienteId());
                if(ok==null || ok==0) throw new org.springframework.security.access.AccessDeniedException("El registro relacionado no pertenece a tu cliente.");
            }
        }
    }
}
