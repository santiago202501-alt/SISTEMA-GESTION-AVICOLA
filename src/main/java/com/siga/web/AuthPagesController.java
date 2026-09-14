package com.siga.web;

import com.siga.security.SigaUser;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@Controller
public class AuthPagesController {
    private final JdbcTemplate jdbc; private final PasswordEncoder encoder;
    public AuthPagesController(JdbcTemplate jdbc, PasswordEncoder encoder){this.jdbc=jdbc;this.encoder=encoder;}
    @GetMapping("/login") public String login(@RequestParam(required=false) String error,@RequestParam(required=false) String logout,Model model){
        if(error!=null) model.addAttribute("error","Correo o contraseña incorrectos.");
        if(logout!=null) model.addAttribute("success","Sesión cerrada correctamente.");
        return "login";
    }
    @GetMapping("/register") public String register(Model model){model.addAttribute("form",Map.of());return "register";}
    @PostMapping("/register") @Transactional public String doRegister(@RequestParam Map<String,String> f,Model model){
        String email=f.getOrDefault("email","").trim();
        if(email.isBlank() || f.getOrDefault("password","").length()<6){model.addAttribute("error","Completa el correo y una contraseña de mínimo 6 caracteres.");model.addAttribute("form",f);return "register";}
        Integer exists=jdbc.queryForObject("SELECT COUNT(*) FROM usuarios WHERE email=?",Integer.class,email);
        if(exists!=null && exists>0){model.addAttribute("error","Ese correo ya está registrado.");model.addAttribute("form",f);return "register";}
        jdbc.update("INSERT INTO clientes(nombre,telefono,email,activo) VALUES (?,?,?,1)",f.getOrDefault("empresa","Cliente SIGA"),f.get("telefono"),email);
        Long clienteId=jdbc.queryForObject("SELECT LAST_INSERT_ID()",Long.class);
        Long roleId=jdbc.queryForObject("SELECT id FROM roles WHERE LOWER(nombre)='cliente' LIMIT 1",Long.class);
        jdbc.update("INSERT INTO usuarios(nombre_completo,documento,email,telefono,password_hash,rol_id,activo,cliente_id) VALUES (?,?,?,?,?,?,1,?)",
                f.get("nombre_completo"),f.get("documento"),email,f.get("telefono"),encoder.encode(f.get("password")),roleId,clienteId);
        return "redirect:/login?registered";
    }
}
