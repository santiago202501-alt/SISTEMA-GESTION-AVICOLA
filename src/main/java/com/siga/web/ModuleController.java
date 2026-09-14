package com.siga.web;

import com.siga.data.*;import com.siga.security.SigaSecurity;
import org.springframework.security.access.prepost.PreAuthorize;import org.springframework.security.core.Authentication;import org.springframework.stereotype.Controller;import org.springframework.ui.Model;import org.springframework.web.bind.annotation.*;
import java.util.*;

@Controller
@RequestMapping("/modules")
public class ModuleController {
  private final ModuleRegistry registry; private final DataService data;
  public ModuleController(ModuleRegistry registry,DataService data){this.registry=registry;this.data=data;}
  @GetMapping("/{key}") public String list(@PathVariable String key,Authentication a,Model model){
    var m=registry.get(key); require(a,m.permission()); var rows=data.list(m,SigaSecurity.user(a));
    model.addAttribute("module",m);model.addAttribute("rows",rows);model.addAttribute("user",SigaSecurity.user(a));model.addAttribute("canCreate",has(a,m.permission().replace(".ver",".crear")));model.addAttribute("canEdit",has(a,m.permission().replace(".ver",".editar")));model.addAttribute("canDelete",has(a,m.permission().replace(".ver",".eliminar")));return "module/list";
  }
  @GetMapping("/{key}/new") public String form(@PathVariable String key,Authentication a,Model model){
    var m=registry.get(key); require(a,m.permission().replace(".ver",".crear")); model.addAttribute("module",m);model.addAttribute("row",Map.of());model.addAttribute("user",SigaSecurity.user(a));return "module/form";
  }
  @GetMapping("/{key}/{id}/edit") public String edit(@PathVariable String key,@PathVariable long id,Authentication a,Model model){
    var m=registry.get(key); require(a,m.permission().replace(".ver",".editar")); model.addAttribute("module",m);model.addAttribute("row",data.one(m,id,SigaSecurity.user(a)));model.addAttribute("user",SigaSecurity.user(a));return "module/form";
  }
  @PostMapping("/{key}/save") public String save(@PathVariable String key,@RequestParam(defaultValue="0") long id,@RequestParam Map<String,String> form,Authentication a){
    var m=registry.get(key); String p=m.permission().replace(".ver", id==0?".crear":".editar");require(a,p);form.remove("id"); data.save(m,id,form,SigaSecurity.user(a)); return "redirect:/modules/"+key;
  }
  @PostMapping("/{key}/{id}/delete") public String delete(@PathVariable String key,@PathVariable long id,Authentication a){
    var m=registry.get(key);require(a,m.permission().replace(".ver",".eliminar"));data.delete(m,id,SigaSecurity.user(a));return "redirect:/modules/"+key;
  }
  private boolean has(Authentication a,String p){ return a.getAuthorities().stream().anyMatch(x->x.getAuthority().equals(p)); }
  private void require(Authentication a,String p){ if(!has(a,p)) throw new org.springframework.security.access.AccessDeniedException("Sin permiso: "+p); }
}
