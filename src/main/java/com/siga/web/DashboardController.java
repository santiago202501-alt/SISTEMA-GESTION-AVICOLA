package com.siga.web;

import com.siga.data.ModuleRegistry;
import com.siga.security.SigaSecurity;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.*;

@Controller
public class DashboardController {
  private final JdbcTemplate jdbc; private final ModuleRegistry modules;
  public DashboardController(JdbcTemplate jdbc, ModuleRegistry modules){this.jdbc=jdbc;this.modules=modules;}
  @GetMapping("/dashboard") public String dashboard(Authentication auth, Model model){
    var u=SigaSecurity.user(auth); boolean admin=u.getAuthorities().stream().anyMatch(a->a.getAuthority().equals("ROLE_ADMINISTRADOR"));
    Map<String,Integer> counts=new LinkedHashMap<>();
    for(var m:modules.all()){
      try { String sql="SELECT COUNT(*) FROM "+m.table(); List<Object> a=new ArrayList<>(); if(!admin){sql+=" WHERE cliente_id=?";a.add(u.getClienteId());} counts.put(m.key(),jdbc.queryForObject(sql,Integer.class,a.toArray())); } catch(Exception e){counts.put(m.key(),0);}
    }
    model.addAttribute("user",u); model.addAttribute("counts",counts); model.addAttribute("modules",modules.all());
    return "dashboard";
  }
}
