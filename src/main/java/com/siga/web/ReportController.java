package com.siga.web;

import com.siga.security.SigaSecurity;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.util.*;

@Controller
public class ReportController {
    private final JdbcTemplate jdbc;
    public ReportController(JdbcTemplate jdbc){this.jdbc=jdbc;}

    @GetMapping("/reportes")
    public String reportes(Model m, org.springframework.security.core.Authentication a){
        var u=SigaSecurity.user(a); require(a,"reportes.ver");
        boolean admin=u.getAuthorities().stream().anyMatch(x->x.getAuthority().equals("ROLE_ADMINISTRADOR"));
        List<Object> ar=new ArrayList<>(); String w="";
        if(!admin){w=" WHERE cliente_id=?";ar.add(u.getClienteId());}
        m.addAttribute("galpones",jdbc.queryForObject("SELECT COUNT(*) FROM galpones"+w,Integer.class,ar.toArray()));
        m.addAttribute("agua",jdbc.queryForObject("SELECT COALESCE(SUM(litros),0) FROM registros_agua"+w,Double.class,ar.toArray()));
        m.addAttribute("alimento",jdbc.queryForObject("SELECT COALESCE(SUM(kilogramos),0) FROM registros_alimento"+w,Double.class,ar.toArray()));
        m.addAttribute("mortalidad",jdbc.queryForObject("SELECT COALESCE(SUM(cantidad),0) FROM mortalidad"+w,Double.class,ar.toArray()));
        m.addAttribute("user",u); return "reportes";
    }

    @GetMapping("/reportes/csv")
    public void csv(HttpServletResponse r,org.springframework.security.core.Authentication a)throws IOException{
        var u=SigaSecurity.user(a); require(a,"reportes.generar");
        boolean admin=u.getAuthorities().stream().anyMatch(x->x.getAuthority().equals("ROLE_ADMINISTRADOR"));
        r.setContentType("text/csv");r.setCharacterEncoding("UTF-8");r.setHeader("Content-Disposition","attachment; filename=reportes_siga.csv");
        var out=r.getWriter();out.println("modulo,total");
        for(String t:List.of("galpones","registros_agua","registros_alimento","registros_amoniaco","mortalidad","inventario","movimientos_inventario","alertas")){
            String sql="SELECT COUNT(*) FROM "+t+(admin?"":" WHERE cliente_id=?");
            Long n=admin?jdbc.queryForObject(sql,Long.class):jdbc.queryForObject(sql,Long.class,u.getClienteId());
            out.println(t+","+n);
        }
    }
    private void require(org.springframework.security.core.Authentication a,String p){if(!a.getAuthorities().stream().anyMatch(x->x.getAuthority().equals(p))) throw new org.springframework.security.access.AccessDeniedException("Sin permiso: "+p);}
}
