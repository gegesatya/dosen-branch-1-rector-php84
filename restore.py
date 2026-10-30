import json

with open(r"C:\Users\musli\.gemini\antigravity-ide\brain\07598e6a-07a1-4b48-a1cc-61c5e1270a43\.system_generated\logs\transcript.jsonl", "r", encoding="utf-8") as f:
    for line in f:
        data = json.loads(line)
        if data.get("step_index") == 8 and data.get("type") == "VIEW_FILE":
            content = data["content"]
            lines = content.split("\n")
            original_code = []
            for l in lines:
                if l.startswith("The following code has been modified") or l.startswith("Created At") or l.startswith("Completed At") or l.startswith("File Path") or l.startswith("Total Lines") or l.startswith("Total Bytes") or l.startswith("Showing lines") or l.startswith("The above content"):
                    continue
                if ":" in l:
                    idx = l.index(":")
                    code = l[idx+2:]
                    original_code.append(code)
            with open(r"c:\laragon\www\wbi\server\dosen\application\controllers\C_tracking_kegiatan.php", "w", encoding="utf-8") as out:
                out.write("\n".join(original_code))
            print("Restored!")
            break

