import sys
import os

# Test asyncio fix
print("Testing asyncio fix...")

# Apply fix
if sys.platform == 'win32':
    import asyncio
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    print("✅ Asyncio policy set to WindowsSelectorEventLoopPolicy")

os.environ['TQDM_DISABLE'] = '1'
print("✅ TQDM disabled")

# Now try to import problematic modules
try:
    from sentence_transformers import SentenceTransformer
    print("✅ sentence_transformers imported successfully!")
except Exception as e:
    print(f"❌ Failed to import: {e}")
    sys.exit(1)

print("\n🎉 All imports work! The fix is successful.")
