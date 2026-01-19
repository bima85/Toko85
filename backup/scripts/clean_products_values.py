#!/usr/bin/env python3
import re
from pathlib import Path

INFILE = Path('products_values_new.sql')
OUTFILE = Path('products_values_cleaned.sql')

def find_values_block(text):
    m = re.search(r"VALUES\s*(\(.*\)\s*;)$", text, flags=re.S | re.I)
    if m:
        return text[:m.start(1)], m.group(1)
    # fallback: find first 'VALUES' and last semicolon
    idx = text.upper().find('VALUES')
    if idx == -1:
        raise SystemExit('VALUES keyword not found')
    header = text[:idx+6]
    rest = text[idx+6:]
    return header, rest

def split_tuples(values_block):
    # values_block includes starting '(' and ends with ');' possibly
    body = values_block.strip()
    if body.endswith(';'):
        body = body[:-1]
    # normalize separators '),\n(' or '), (' into a unique token
    body = re.sub(r"\),\s*\n\s*\(", ")<<SPLIT>>(",
                  body)
    body = re.sub(r"\),\s*\(", ")<<SPLIT>>(",
                  body)
    parts = [p.strip() for p in body.split('<<SPLIT>>')]
    return parts

def parse_tuple(t):
    # t is like "(1, 'ABC', NULL, 'x')"
    s = t.strip()
    if s.startswith('(') and s.endswith(')'):
        s = s[1:-1]
    fields = []
    cur = ''
    in_quote = False
    i = 0
    while i < len(s):
        ch = s[i]
        if ch == "'":
            if in_quote:
                # possible escaped ''
                if i+1 < len(s) and s[i+1] == "'":
                    cur += "'"
                    i += 2
                    continue
                else:
                    in_quote = False
                    i += 1
                    continue
            else:
                in_quote = True
                i += 1
                continue
        if not in_quote and ch == ',':
            fields.append((cur, False))
            cur = ''
            i += 1
            continue
        cur += ch
        i += 1
    fields.append((cur, in_quote))
    # Post-process: determine which fields were quoted
    normalized = []
    raw = s
    # We'll re-parse with a simpler method: iterate capturing quoted/unquoted
    res = []
    cur = ''
    in_q = False
    i = 0
    while i < len(s):
        ch = s[i]
        if ch == "'":
            if in_q and i+1 < len(s) and s[i+1] == "'":
                cur += "'"
                i += 2
                continue
            in_q = not in_q
            i += 1
            continue
        if not in_q and ch == ',':
            res.append((cur, False))
            cur = ''
            i += 1
            continue
        cur += ch
        i += 1
    res.append((cur, False))
    # Trim spaces and detect quoted earlier by checking surrounding chars in original
    fields_final = []
    idx = 0
    for part in res:
        val = part[0].strip()
        was_quoted = False
        if val.upper() == 'NULL':
            fields_final.append((None, False))
            continue
        # detect if original had quotes by checking for leading/trailing single quote in original tuple
        # approximate: if original contains "'" around this value
        fields_final.append((val, True))
    return fields_final

def clean_field(val):
    if val is None:
        return None
    v = val
    # remove leading/trailing spaces
    v = v.strip()
    # if it's the literal string NULL
    if v.upper() == 'NULL':
        return None
    # remove leading single quotes left from bad import
    v = re.sub(r"^[\s']+", '', v)
    v = re.sub(r"[\s']+$", '', v)
    # collapse multiple spaces
    v = re.sub(r"\s+", ' ', v)
    return v

def reconstruct_tuple(fields_orig):
    out_fields = []
    for val, quoted in fields_orig:
        if val is None:
            out_fields.append('NULL')
            continue
        cleaned = clean_field(val)
        if cleaned is None:
            out_fields.append('NULL')
            continue
        # escape single quotes
        cleaned = cleaned.replace("'", "''")
        out_fields.append("'{}'".format(cleaned))
    return '(' + ', '.join(out_fields) + ')'

def main():
    txt = INFILE.read_text(encoding='utf-8')
    try:
        header, values_block = find_values_block(txt)
    except SystemExit as e:
        print('Failed to find VALUES block:', e)
        return
    tuples = split_tuples(values_block)
    cleaned_parts = []
    for t in tuples:
        fields = parse_tuple(t)
        # fields is list of (val, quoted_flag) but parse_tuple returns val strings
        # We'll treat every non-NULL as quoted-string candidate
        fields_for_recon = []
        s = t.strip()
        if s.startswith('(') and s.endswith(')'):
            body = s[1:-1]
        else:
            body = s
        # simple split respecting quotes done above; reuse parse results as raw strings
        # use regex to split by commas not inside quotes
        parts = []
        cur = ''
        in_q = False
        i = 0
        while i < len(body):
            ch = body[i]
            if ch == "'":
                if in_q and i+1 < len(body) and body[i+1] == "'":
                    cur += "'"
                    i += 2
                    continue
                in_q = not in_q
                i += 1
                continue
            if not in_q and ch == ',':
                parts.append(cur)
                cur = ''
                i += 1
                continue
            cur += ch
            i += 1
        parts.append(cur)
        for p in parts:
            p = p.strip()
            if p.upper() == 'NULL':
                fields_for_recon.append((None, False))
                continue
            if p.startswith("'") and p.endswith("'") and len(p) >= 2:
                inner = p[1:-1].replace("''", "'")
                fields_for_recon.append((inner, True))
            else:
                fields_for_recon.append((p, False))
        cleaned = reconstruct_tuple(fields_for_recon)
        cleaned_parts.append(cleaned)

    out = header.strip() + '\nVALUES\n' + ',\n'.join(cleaned_parts) + ';\n'
    OUTFILE.write_text(out, encoding='utf-8')
    print('Wrote', OUTFILE)

if __name__ == '__main__':
    main()
