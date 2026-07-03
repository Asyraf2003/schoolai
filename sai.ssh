#!/bin/bash

COUNTER_FILE=".commit_counter"

# Cek komponen state, jika belum ada set awal ke 5
if [ ! -f "$COUNTER_FILE" ]; then
    echo 5 > "$COUNTER_FILE"
fi

CURRENT_COUNT=$(cat "$COUNTER_FILE")

# Eksekusi Git rangkaian
git add .
git commit -m "$CURRENT_COUNT"
git push

# Audit status output command git terakhir
if [ $? -eq 0 ]; then
    NEXT_COUNT=$((CURRENT_COUNT + 1))
    echo $NEXT_COUNT > "$COUNTER_FILE"
    echo "Success: Commit '$CURRENT_COUNT' telah di-push. Counter berikutnya: $NEXT_COUNT"
else
    echo "Error: Git push gagal. State counter tetap di $CURRENT_COUNT"
    exit 1
fi
