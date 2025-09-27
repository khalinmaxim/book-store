#!/usr/bin/env bash
set -euo pipefail

echo "[migrator] Waiting for db..."
until pg_isready -h "${PGHOST:-db}" -p "${PGPORT:-5432}" -U "${PGUSER:-app}" -d "${PGDATABASE:-bookstore}" >/dev/null 2>&1; do
  sleep 1
done

echo "[migrator] Ensure schema_migrations table"
psql <<'SQL'
CREATE TABLE IF NOT EXISTS schema_migrations (
  id SERIAL PRIMARY KEY,
  filename TEXT UNIQUE NOT NULL,
  checksum TEXT NOT NULL,
  applied_at TIMESTAMP NOT NULL DEFAULT now()
);
SQL

apply_file () {
  local f="$1"
  local base="$(basename "$f")"
  local sum
  sum="$(sha256sum "$f" | awk '{print $1}')"

  # уже применяли?
  local exists
  exists="$(psql -tAc "SELECT 1 FROM schema_migrations WHERE filename='${base}' AND checksum='${sum}'")"
  if [[ "$exists" == "1" ]]; then
    echo "[migrator] Skip ${base} (already applied, checksum match)"
    return
  fi

  # Если filename совпадает, но чек-сумма другая — применим как новую версию (файл нужно переименовывать)
  local same_name
  same_name="$(psql -tAc "SELECT 1 FROM schema_migrations WHERE filename='${base}'")"
  if [[ "$same_name" == "1" ]]; then
    echo "[migrator] WARNING: ${base} was applied with a different checksum earlier. Rename migration file to apply changes."
    return
  fi

  echo "[migrator] Applying ${base}..."
  psql -v ON_ERROR_STOP=1 -f "$f"
  psql -v ON_ERROR_STOP=1 -c "INSERT INTO schema_migrations(filename, checksum) VALUES ('${base}', '${sum}');"
  echo "[migrator] Done ${base}"
}

shopt -s nullglob
files=(/migrations/*.sql)
IFS=$'\n' sorted=($(sort <<<"${files[*]}"))
unset IFS

if [[ ${#sorted[@]} -eq 0 ]]; then
  echo "[migrator] No migrations found"
else
  for f in "${sorted[@]}"; do
    apply_file "$f"
  done
fi

echo "[migrator] All migrations applied"
