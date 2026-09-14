package com.siga.data;

import java.util.List;

public record ModuleDefinition(String key, String title, String icon, String table, String permission,
                               List<Field> fields, String displayColumns) {
    public record Field(String name, String label, String type, boolean required) {}
}
